#!/bin/bash

KEY_ALIAS=$1
KEYSTORE_PASS=$2
KEYSTORE_IN=$3
KEYSTORE_OUT=$4

# Export certificate
keytool -exportcert \
  -alias "$KEY_ALIAS" \
  -keystore "$KEYSTORE_IN" \
  -storepass "$KEYSTORE_PASS" \
  -rfc \
  -file certificate.pem

# Export to PKCS#12
keytool -importkeystore \
  -srckeystore "$KEYSTORE_IN" \
  -srcalias "$KEY_ALIAS" \
  -srcstorepass "$KEYSTORE_PASS" \
  -destkeystore keystore.p12 \
  -deststoretype PKCS12 \
  -deststorepass "$KEYSTORE_PASS"

# Import into new JKS keystore
keytool -importkeystore \
  -destkeystore "$KEYSTORE_OUT" \
  -deststoretype JKS \
  -deststorepass "$KEYSTORE_PASS" \
  -srckeystore keystore.p12 \
  -srcstoretype PKCS12 \
  -srcstorepass "$KEYSTORE_PASS" \
  -alias "$KEY_ALIAS"

keytool -list -v -keystore "$KEYSTORE_OUT" -storepass "$KEYSTORE_PASS"

# Clean up temporary files
rm certificate.pem keystore.p12

