# ใช้ PHP 8.2 พร้อม Apache
FROM php:8.2-apache

# เปิดใช้งาน Apache mod_rewrite (สำหรับ URL rewrite)
RUN a2enmod rewrite

# ติดตั้ง PHP Extensions ที่จำเป็นสำหรับ MySQL และ PDO
RUN docker-php-ext-install pdo pdo_mysql mysqli

# คัดลอกโค้ดทั้งหมดเข้า Container
COPY . /var/www/html/

# ตั้งค่า Permission โฟลเดอร์เว็บ
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80