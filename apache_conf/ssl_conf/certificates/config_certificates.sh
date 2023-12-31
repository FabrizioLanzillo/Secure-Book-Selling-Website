#!/bin/sh

# This take the domain from the stdin
if [ "$#" -ne 1 ]
then
	echo "Usage: Please insert a domain"
	exit 1
fi

DOMAIN=$1

# ----------------------------------- CA CERT GENERATION ------------------------------------------------
# generation of the CA private key
openssl genrsa -des3 -out SNH_CA.key 2048
# generation of the CA certificate
openssl req -x509 -new -nodes -key SNH_CA.key -sha256 -days 1825 -out SNH_CA.pem
# copy of the cert in the ca-certificate folder in order to update it
sudo cp SNH_CA.pem /usr/local/share/ca-certificates/SNH_CA.crt
# check the correctness of the certificate
openssl x509 -in /usr/local/share/ca-certificates/SNH_CA.crt -noout -text
sudo update-ca-certificates
# check if our certificate is installed in the os
awk -v cmd='openssl x509 -noout -subject' '/BEGIN/{close(cmd)};{print | cmd}' < /etc/ssl/certs/ca-certificates.crt | grep UNIPI

# ----------------------------------- SERVER CERT GENERATION --------------------------------------------
# generation of the SERVER private key
openssl genrsa -out $DOMAIN.key 2048
# generation of the SERVER certificate signing request
openssl req -new -key $DOMAIN.key -out $DOMAIN.csr
# creation of an  X509 V3 certificate extension config file
cat > $DOMAIN.ext << EOF
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names
[alt_names]
DNS.1 = *.$DOMAIN
EOF
# generation by the CA of the certificate for the SERVER
openssl x509 -req -in $DOMAIN.csr -CA SNH_CA.pem -CAkey SNH_CA.key -CAcreateserial \
-out $DOMAIN.crt -days 825 -sha256 -extfile $DOMAIN.ext

# folder path fix
mkdir ca
mv SNH_CA.* ca
mkdir bookselling
mv bookselling.* bookselling


