Synchronized-table - управление таблицей, двусторонняя синхронизация данных с Google Таблицей.

Требования (технологии): PHP 8.4, Composer, Laravel 12, MySQL (или другая СУБД)

Чтобы запустить этот проект локально, выполните следующие шаги:

1.  **Клонируйте репозиторий:**
    ```bash
    git clone https://github.com/temastraik/synchronized-table
    cd synchronized-table
    ```

2.  **Установите зависимости PHP:**
    ```bash
    composer install
    ```

3.  **Настройте environment-файл:**

    Скопируйте файл `.env.example` в `.env` и настройте базовые параметры Laravel.
    
    *Настройте Базу данных по вашим параметрам*
    
    <img width="172" height="116" alt="image" src="https://github.com/user-attachments/assets/a7f2a96d-fabc-438c-a2b0-2736b2179f30" />

    *Настройте URL к Google таблице по умолчанию*
    
    <img width="352" height="39" alt="image" src="https://github.com/user-attachments/assets/8ec9e06d-9958-46fb-a6a8-1107730554b7" />


5.  **Запустите миграции:**
    ```bash
    php artisan migrate
    ```

Функционал:

- Генерация, создание и удаление строк в таблице;

- Синхронизация с Google Таблицей.
