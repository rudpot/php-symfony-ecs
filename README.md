# PHP Symfony ECS example

Test php symfony deploy on ECS.

## Testing symfony app locally

* clone the repo
* run the docker with 
  ```
  cd php-test-app
  docker-compose up
  ```
* check that there is a default splash page at http://localhost:8000
* check that there are two custom endpoints at http://localhost:8000/lucky/number and http://localhost:8000/lucky/lottery
* connect to the running container to access `bin/console` command. Find the right container with `docker ps` and then connect to it using the hash of the running container, e.g.:
  ```
  docker exec -it e84fcfaa2a0a /bin/bash
  ```
* there is an endpoint /aws/s3test which will try to list s3 buckets and read the content of a named bucket. Currently that bucket name is hardcoded so will likel fail for you.

### How we got here

* use bitnami docker image - https://hub.docker.com/r/bitnami/symfony/
* see the _local workspace_ instructions for local testing. Note that docker-compose may not play nice with docker login so the `docker compose up` might fail with a 429. If it does user `docker login` and `docker pull` to get the two included images
* add https://github.com/aws/aws-sdk-php-symfony
```
docker exec -it e84fcfaa2a0a composer require aws/aws-sdk-php-symfony
```

## Docker

* Build docker image with the app installed

  ```bash
  docker build -t private-bitnami-php-symphony ./
  ```

* Create repo

  ```bash
  # Create repo and get URI
  aws ecr create-repository --repository-name ecs-test-repo > /tmp/ecr.json
  REPO_URI=$(jq -r .repository.repositoryUri /tmp/ecr.json)
  echo $REPO_URI
  ```

* Get login credentials and push

  ```bash
  aws ecr get-login-password | docker login --username AWS --password-stdin ${REPO_URI}
  # aws_account_id.dkr.ecr.region.amazonaws.com
  docker tag private-bitnami-php-symphony ${REPO_URI}
  docker push ${REPO_URI}
  ```

## Set up ECS cluster

The included `cloudformation/fargate.yaml` CFN template originated from [1Strategy](https://github.com/1Strategy/fargate-cloudformation-example/blob/master/fargate.yaml). 

Make sure you have a domain name, and a hosted zone for it in route53 in the same account. Also make sure you have and ACM certificate that covers the subdomain under which you want to run the service.

Populate a file named `parmeters.json` with relevant information based on the `Parameters section` of the `fargate.yaml` file. It should looks something like this:

```json
[
    {
        "ParameterKey": "VPC",
        "ParameterValue": "vpc-..."
    },
    {
        "ParameterKey": "LbSubnetA",
        "ParameterValue": "subnet-..."
    },
    {
        "ParameterKey": "LbSubnetB",
        "ParameterValue": "subnet-..."
    },
    {
        "ParameterKey": "TaskSubnetA",
        "ParameterValue": "subnet-..."
    },
    {
        "ParameterKey": "TaskSubnetB",
        "ParameterValue": "subnet-..."
    },
    {
        "ParameterKey": "Certificate",
        "ParameterValue": "arn:aws:acm:us-west-2:111122223333:certificate/00000000-0000-0000-0000-000000000000"
    },
    {
        "ParameterKey": "Image",
        "ParameterValue": "111122223333.dkr.ecr.us-west-2.amazonaws.com/ecs-test-repo"
    },
    {
        "ParameterKey": "ServiceName",
        "ParameterValue": "php-symfony"
    },
    {
        "ParameterKey": "ContainerPort",
        "ParameterValue": "8000"
    },
    {
        "ParameterKey": "HealthCheckPath",
        "ParameterValue": "/lucky/number"
    },
    {
        "ParameterKey": "HostedZoneName",
        "ParameterValue": "my.domain"
    },
    {
        "ParameterKey": "Subdomain",
        "ParameterValue": "ecs-php-symphony"
    }
]
```


then create a cloudforamtion stack:

```bash
cd cloudformation
aws cloudformation create-stack \
  --stack-name ecs-php-symfony \
  --template-body file://fargate.yaml \
  --parameters file://parameters.json \
  --capabilities CAPABILITY_NAMED_IAM
aws cloudformation wait stack-create-complete \
  --stack-name ecs-php-symfony
```

Check the Cloudformation console for the stack output to get the service URL. Then check the `/lucky/number` endpoint to validate the service is working and the `/aws/s3test` endpoint to validate that you can control access to AWS resources via the "TaskRole" IAM policies.

## Cleanup

* Delete the fargate ECS stack
  ```bash
  aws cloudformation delete-stack \
    --stack-name ecs-php-symfony
  aws cloudformation wait stack-delete-complete \
    --stack-name ecs-php-symfony
  ```

* optionally delete the ACM certificate - via the console because that's the way it was created
* optionally delete the ECR repository - via the console because image deletion is easier that way
