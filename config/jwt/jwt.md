# JWT

To generate a private key use the command

`openssl genrsa -out config/jwt/private.pem -aes256 4096`

and enter passpharase given in .env file

for public key enter :

`openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem`