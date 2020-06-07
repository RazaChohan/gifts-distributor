# gifts-distributor

# Installation

####  1. First, clone this repository:

```bash
$ git clone https://github.com/RazaChohan/gifts-distributor.git
```

####  2. Next, kindly add following entry in your `/etc/hosts` file:

```bash
127.0.0.1 http://api.gifts_distributor.local/
```

####  3. Create docker containers:

```bash
$ docker-compose up -d
```

#### 4. Confirm three running containers for php, nginx, & mysql:

```bash
$ docker-compose ps 
```

#### 5. Install composer packages:

```bash
$ docker-compose run php composer install 
```
#### 6. Create Database schema:

```bash
$ docker-compose run php php artisan migrate 
```

#### 7. Load data is Database from json files:

```bash
$ docker-compose run php php artisan db:seed
```

#### 8. Sync user purchased products call:
```bash
 $ curl -X POST -H "Content-Type: application/json" http://api.gifts_distributor.local/employee/gift -d '{"employee_id": 1}'
```

#### 9. Solution Explanation:
- Two insertions of same gift is handled by unique constraint on database and in codebase the exception is handled 
  and getGift is called again to assign another gift to user. (Handling high load problem).
- Gift selection and categories matching with interest of employee is handled by following query.
```bash
      SELECT Group_concat(gc.category_id) AS categories, 
                                                       gc.gift_id 
                                                FROM   gifts AS g 
                                                       JOIN gift_categories gc 
                                                         ON gc.gift_id = g.id 
                                                WHERE  gc.category_id IN ( 5,7,9 ) 
                                                       AND g.id NOT IN (SELECT gift_id 
                                                                        FROM   employee_gifts) 
                                                GROUP  BY gc.gift_id 
                                                ORDER  BY Length(Group_concat(gc.category_id)) DESC 
                                                LIMIT  1;
```                                                
 - User can get only one gift. API checks whether a gift is already assigned to employee or not
 - If a new features is required to return the gift. In this case simply need to delete the record from employee_gifts 
   table. Employee_gifts table is used as a many to many relationship (more than one gift can be assigned in future)

Application logs can be found on following locations:
```bash
  logs/nginx
  application/storage/logs
```
For docker image I have used https://github.com/eko/docker-symfony repo and tweaked it a bit as per my requirements.
