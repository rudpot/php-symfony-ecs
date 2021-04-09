FROM docker.io/bitnami/symfony:1
LABEL maintainer "Rudolf Potucek <rudpot@amazon.com>"

COPY php-test-app /app

EXPOSE 8000

WORKDIR /app
ENTRYPOINT [ "/app-entrypoint.sh" ]
CMD [ "/run.sh" ]
