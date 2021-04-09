# private-php-symfony-ecs

Test php symfony deploy on ECS.

## Testing content of this repo

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

## How we got here

* use bitnami docker image - https://hub.docker.com/r/bitnami/symfony/
* see the _local workspace_ instructions for local testing. Note that docker-compose may not play nice with docker login so the `docker compose up` might fail with a 429. If it does user `docker login` and `docker pull` to get the two included images


```
```
