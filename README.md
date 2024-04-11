1) To install project please use dockers:
 - bitnami/phpmyadmin:latest
 - bitnami/laravel:latest
 - bitnami/mariadb:latest

2) Run docker containers
3) Clone the project
4) Set DB connection config params if you need
5) Run the cli command: php artisan migrate
6) You can use this command to make post request to endpoint
   curl -X POST -H "Content-Type: application/json" -d '{
   "objects": [
   {
   "ref": "T-1",
   "name": "test",
   "description": null
   },
   {
   "ref": "T-2",
   "name": "test",
   "description": "Test description"
   }
   ]
   }' http://localhost:8000/api/demo/test

Please check that port for request and started server is the same
