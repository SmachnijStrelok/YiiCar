FROM nginx:latest

RUN apt-get update && \
    apt-get install -y \
    certbot


EXPOSE 80

ARG USE_HTTPS
ARG SITE_NAME=_
ENV SITE_NAME ${SITE_NAME}

RUN rm /etc/nginx/nginx.conf
COPY ./nginx_http.conf /etc/nginx/nginx_http.conf
COPY ./deploy.sh /deploy.sh
RUN /bin/bash /deploy.sh

ENTRYPOINT ["nginx", "-g", "daemon off;"]
