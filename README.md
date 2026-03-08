# Veb aplikacija za izložbe fotografija

Ova aplikacija omogućava pregled i upravljanje izložbama fotografija.  
Korisnici mogu da pregledaju izložbe, vide detalje izložbe, galeriju fotografija i rezervišu mesto za događaj.

Aplikacija je razvijena kao projekat iz predmeta **Internet tehnologije**.


# Tehnologije

Projekat koristi sledeće tehnologije:

Frontend:
- React
- React Router
- Axios
- Leaflet

Backend:
- Php
- Laravel
- Laravel Sanctum
- MySQL

DevOps:
- Docker
- Docker Compose

Dokumentacija API-ja:
- Swagger(OpenAPI)


# Eksterni API servisi

Aplikacija koristi sledeće eksterne API-je:

1. **OpenStreetMap / Leaflet**
   - koristi se za prikaz mape na stranici detalja izložbe

2. **Nominatim Geocoding API**
   - koristi se za konverziju naziva lokacije u geografske koordinate

3. **Open-Meteo Weather API**
   - koristi se za prikaz trenutnih vremenskih uslova na lokaciji izložbe


# Funkcionalnosti aplikacije

- registracija i prijava korisnika
- pregled svih izložbi
- filtriranje izložbi
- prikaz detalja izložbe
- pregled galerije fotografija
- rezervacija mesta na izložbi
- upravljanje fotografijama
- navigacija
- upload fajlova


# Pokretanje aplikacije

Aplikacija je dockerizovana i pokreće se pomoću **Docker Compose**.

## 1. Kloniranje repozitorijuma

```bash
git clone https://github.com/elab-development/internet-tehnologije-2025-appzaizlozbefotografija_2021_0466
cd internet-tehnologije-2025-appzaizlozbefotografija_2021_0466
```
## 2. Pokretanje aplikacije pomoću Docker-a

Aplikacija koristi Docker Compose za pokretanje svih servisa sistema (frontend, backend i baza podataka).
Iz root direktorijuma projekta potrebno je pokrenuti sledeću komandu: docker compose up -d

## 3. Pokretanje migracija baze podataka

Nakon prvog pokretanja sistema potrebno je izvršiti migracije kako bi se kreirala struktura baze podataka.
Komanda za pokretanje migracija: docker compose exec backend php artisan migrate

## 4. Pokretanje seedera

Seederi služe za ubacivanje početnih podataka u bazu.
Pokretanje seedera vrši se sledećom komandom: docker compose exec backend php artisan db:seed

## 5. Zaustavljanje aplikacije

Za zaustavljanje svih Docker kontejnera koristi se komanda: docker compose down
