# E-PRESENCE
The development of technology in the modern era has encouraged various institutions, including educational institutions, to transform towards digitalization in various aspects of their operations. One area that has experienced significant changes is the student attendance system in schools.
The manual attendance method has many limitations, is slow in the recapitulation process, and is less effective in monitoring student discipline in real-time. Therefore, an innovative solution is needed to increase speed, accuracy, and transparency in the attendance process.

With the implementation of this E-Presence system, it is expected to improve student discipline, make it easier for schools to recap attendance, and create a more professional, modern, and high-integrity educational environment.

## Getting Started
You can download the ZIP or clone using this Github URL. I state that this is only the first release version that I have implemented open source!

### Prerequisites
List:
- **Java 11+ version**
- **Supports C#.NET**
- **NodeJS**
- **Docker (You don't need to install the required version of php or composer or etc, just install docker, and I have isolated it in a container and run it on localhost port 2025.)**

### Items Included in Docker
1. MySQL (Latest)
2. PHP (8.3.*)
3. Composer (Latest)
4. PHPMyAdmin (Latest)
5. Nginx (Alpine)

### Installing Web
1. Download this project
2. Extract this project
3. Run `docker compose build` (If you want to use Docker)
4. Run `docker compose up -d` (If you want to use Docker)
5. Go into the `backend` folder
6. Create a Symlink between Frontend and Backend using the code
    > for Linux: `ln -s ../frontend/public public`
    > for PowerShell (Administrator): `New-Item -ItemType SymbolicLink -Path "public" -Target "..\frontend\public"`
7. Run `composer install && npm install`
8. Run `php artisan key:generate`
9. Go into the `root` folder
10. Run `docker compose exec app bash` (If you want to use Docker)
11. Go back into the `backend` folder
12. Run `php artisan migrate --seed`
13. Run `php artisan serve` (Not required if you want to use Docker, just run the container and open in browser `http://localhost:2025/`)
14. Go into the `frontend` folder
15. Run `npm run dev` or `npm run build` (Required for vite JS Axios)

## Contribute
If you want to contribute to this project, then contact email raadeveloperz@gmail.com or WhatsApp +62895383107479

## *NOTE
To get the backend folder, contact the administrator to establish a project collaboration relationship.