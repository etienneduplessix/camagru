FROM nginx:latest

ARG 127.0.0.1

RUN apt-get -y update
RUN apt-get -y install nginx openssl sed

RUN mkdir -p /cert
RUN openssl req -x509 -nodes \
	-out /cert/nginx.crt \
	-keyout /cert/nginx.key \
	-subj "/CN=camagru_cert/C=CZ/L=Praga/O=42/OU=42/UID=theBestest"


COPY conf/nginx.conf /etc/nginx/nginx.conf
RUN cat  /etc/nginx/nginx.conf

#the only port allowed
EXPOSE 8443
