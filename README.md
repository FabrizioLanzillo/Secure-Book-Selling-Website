# Secure Book Selling Website
### SSL/TLS CERTIFICATES GENERATION

* In order to generate the certificates execute:

```
cd apache_conf/ssl_conf/certificates
./config_certificates.sh
```
p.s. remember to give executable permission to the script with ```chmod +x config_certificates.sh```

* After this import the SNH_CA.pem in ```apache_conf/ssl_conf/certificates``` in the browser

### SYSTEM CONFIGURATION

* In order to load the system execute:

```
./handle_services.sh start
```

This command will also start all the services.  
p.s. remember to give executable permission to the script with ```chmod +x handle_service.sh```

* To stop all the services:

```
./handle_services.sh stop
```

* To see the status of the container:

```
./handle_services.sh status
```

### WEBSITE AND SERVICES ACCESS

* Add this rule in the ```/etc/hosts``` file:

```
127.0.0.1 www.bookselling.snh
```

* To access the WebSite type the following URL in the browser:

```
www.bookselling.snh
```

* To access the PhpMyAdmin Portal type the following URL in the browser:

```
localhost:8800
```

* To access the MYSQL DB with a client use these settings:
```
host: localhost
port: 9906
```
