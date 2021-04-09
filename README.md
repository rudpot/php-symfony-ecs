# private-php-symfony-ecs

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