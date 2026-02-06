# TechShop – Instalacija i pokretanje aplikacije

Ovaj dokument opisuje postupak instalacije i lokalnog pokretanja web aplikacije razvijene u Laravel okviru.  
Baza podataka se **uvozi ručno putem XAMPP-a (phpMyAdmin)**.

---

##  Preduvjeti

Za pokretanje aplikacije potrebno je imati instalirano:

- PHP **8.2** ili noviji
- Composer
- Node.js + npm
- MySQL
- Git
- XAMPP (Apache + MySQL)

---

##  Instalacija i pokretanje

###  Kloniranje repozitorija
```bash
git clone <URL-repozitorija>
cd naziv_repozitorija/laravel
composer install
npm install
cp .env.example .env
php artisan key:generate
```
Nakon toga potrebno je ubaciti bazu podataka:


Pokrenuti XAMPP Control Panel

Pokrenuti servise Apache i MySQL

Otvoriti phpMyAdmin

Kreirati novu bazu podataka pod nazivom web_trgovina

Odabrati karticu Import

Učitajti dostavljenu .sql datoteku baze podataka

Pokrenuti uvoz baze

Nakon uspješnog uvoza baza je spremna za korištenje.


Kreiranje storage poveznice (OBAVEZNO)

Za ispravan prikaz slika potrebno je izvršiti sljedeću naredbu:

php artisan storage:link


Napomena:
Laravel pohranjuje uploadane datoteke u direktorij storage/app/public/, dok web preglednik može pristupiti samo datotekama unutar public/ direktorija.
Naredba storage:link stvara simboličku poveznicu:

public/storage → storage/app/public


Simboličke poveznice ne mogu se pohraniti u Git repozitorij jer su specifične za lokalni sustav, stoga je ovu naredbu potrebno izvršiti nakon svakog kloniranja projekta.


Pokretanje aplikacije

Aplikacija se pokreće naredbom:

php artisan serve --host=0.0.0.0 --port=8000


Aplikacija će biti dostupna na adresi:

http://127.0.0.1:8000


## Pokretanje Android aplikacije (za dostavljače)

Android aplikacija namijenjena je **isključivo dostavljačima** te služi za pregled dodijeljenih narudžbi, prikaz detalja narudžbe i potvrdu dostave putem kontrolnog potpisa.

---

###  Preduvjeti

Za pokretanje Android aplikacije potrebno je imati:

- Android Studio
- Instaliran Android SDK
- Emulator ili fizički Android uređaj
- Pokrenutu Laravel web aplikaciju

---

###  Otvaranje projekta u Android Studiju

1. Pokrenuti **Android Studio**
2. Odabrati **Open an existing project**
3. Odabrati direktorij Android aplikacije
4. Pričekati završetak Gradle build procesa

---

###  Postavljanje API adrese (OBAVEZNO)

Android aplikacija komunicira s Laravel backendom putem API-ja.  
Potrebno je **ručno postaviti IP adresu računala** u datoteci: ApiClient.java


U navedenoj datoteci potrebno je pronaći sljedeću liniju i prilagoditi IP adresu računala:

```java
.baseUrl("http://IP_ADRESA_VAŠEG_RAČUNALA:8000/")

```