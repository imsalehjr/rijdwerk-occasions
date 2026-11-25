USE rijdwerk;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE request_status_log; TRUNCATE request_notes; TRUNCATE requests; TRUNCATE car_features; TRUNCATE car_photos; TRUNCATE cars; TRUNCATE models; TRUNCATE admins; TRUNCATE rate_limits; TRUNCATE rdw_cache;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO models (id, brand_id, name, slug) SELECT 1, id, 'Golf', 'golf' FROM brands WHERE name = 'Volkswagen';
INSERT INTO models (id, brand_id, name, slug) SELECT 2, id, 'Corolla Touring Sports', 'corolla-touring-sports' FROM brands WHERE name = 'Toyota';
INSERT INTO models (id, brand_id, name, slug) SELECT 3, id, 'Niro EV', 'niro-ev' FROM brands WHERE name = 'Kia';
INSERT INTO models (id, brand_id, name, slug) SELECT 4, id, '208', '208' FROM brands WHERE name = 'Peugeot';
INSERT INTO models (id, brand_id, name, slug) SELECT 5, id, 'XC60', 'xc60' FROM brands WHERE name = 'Volvo';
INSERT INTO models (id, brand_id, name, slug) SELECT 6, id, '3 Serie Touring', '3-serie-touring' FROM brands WHERE name = 'BMW';
INSERT INTO models (id, brand_id, name, slug) SELECT 7, id, 'Octavia Combi', 'octavia-combi' FROM brands WHERE name = 'Skoda';
INSERT INTO models (id, brand_id, name, slug) SELECT 8, id, 'Clio', 'clio' FROM brands WHERE name = 'Renault';
INSERT INTO models (id, brand_id, name, slug) SELECT 9, id, 'MX-5', 'mx-5' FROM brands WHERE name = 'Mazda';
INSERT INTO models (id, brand_id, name, slug) SELECT 10, id, 'Kuga', 'kuga' FROM brands WHERE name = 'Ford';
INSERT INTO models (id, brand_id, name, slug) SELECT 11, id, 'IONIQ 5', 'ioniq-5' FROM brands WHERE name = 'Hyundai';
INSERT INTO models (id, brand_id, name, slug) SELECT 12, id, 'Touran', 'touran' FROM brands WHERE name = 'Volkswagen';
INSERT INTO models (id, brand_id, name, slug) SELECT 13, id, 'Model 3', 'model-3' FROM brands WHERE name = 'Tesla';
INSERT INTO models (id, brand_id, name, slug) SELECT 14, id, '500', '500' FROM brands WHERE name = 'Fiat';
INSERT INTO models (id, brand_id, name, slug) SELECT 15, id, 'C-Klasse', 'c-klasse' FROM brands WHERE name = 'Mercedes-Benz';

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (1, 'volkswagen-golf-1-5-etsi-life-business-2021', 1, '1.5 eTSI Life Business', 'K482TB', 22950, 2021, '2021-01-01', 58400, 'petrol', 'automatic', 'hatchback', 'Grijs metallic', 110, 1498, NULL, NULL, 5, 5, 1, '2027-01-15', 12, 'Een Golf zoals je hem wilt: één eigenaar, volledig bij de dealer onderhouden en met de soepele 7-traps DSG. De mild-hybride techniek maakt hem zuinig in de stad en stil op de snelweg.

We hebben hem in onze eigen werkplaats nagelopen: nieuwe remblokken voor, verse onderhoudsbeurt en een nieuwe APK. Klaar om weg te rijden.', 'available', 1, 214, NOW() - INTERVAL 4 DAY - INTERVAL 0 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 1, id FROM features WHERE name IN ('Adaptieve cruise control', 'Climate control', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren voor', 'Parkeersensoren achter', 'LED-koplampen', 'Lichtmetalen velgen', 'Digitaal instrumentenpaneel', 'Rijstrookassistent', 'Noodremassistent');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (1, '1b80da1ba068dcf33a849132947f8249.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (1, 'deeb8168abc9a323c3066e9094a4ab89.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (1, '8865541b2a8de3c22500ee6cd07e914d.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (1, '8c12f84af10364cc3b9879e2129adf0e.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (1, '79083637ec7775f7067426c09a3384af.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (2, 'toyota-corolla-touring-sports-1-8-hybrid-active-2020', 2, '1.8 Hybrid Active', 'X217HN', 19450, 2020, '2020-06-08', 84200, 'hybrid', 'automatic', 'estate', 'Wit', 90, 1798, NULL, NULL, 5, 5, 1, '2027-04-15', 12, 'Ruime, betrouwbare stationwagen met de bekende hybride aandrijving van Toyota. Rijdt in de stad een groot deel elektrisch en verbruikt in de praktijk rond 1 op 21.

De bagageruimte van ruim 580 liter maakt hem een ideale gezinsauto. Inclusief complete onderhoudshistorie.', 'available', 0, 167, NOW() - INTERVAL 9 DAY - INTERVAL 37 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 2, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Achteruitrijcamera', 'Apple CarPlay', 'Android Auto', 'Verkeersbordherkenning', 'Rijstrookassistent', 'DAB+ radio', 'Dakrails', 'Isofix');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (2, '95d435527a0afc7965d2451460c2a625.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (2, '53ba5cfd0ee919723c5c2cf598d49167.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (2, 'b3e7b82ffc7f783f7b862ef7d916a38b.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (2, 'f041d3b131e5bc25fdc700ef7c1f6454.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (2, 'c1df80ca2587d8c037f1dd2da38b1497.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (3, 'kia-niro-ev-dynamicline-64-8-kwh-2023', 3, 'DynamicLine 64,8 kWh', 'N906ZL', 27900, 2023, '2023-11-15', 31900, 'electric', 'automatic', 'suv', 'Bosgroen', 150, NULL, 64.8, 460, 5, 5, 1, '2027-07-15', 24, 'Volledig elektrisch met een echte actieradius van ruim 400 kilometer. Snelladen gaat van 10 naar 80 procent in ongeveer 45 minuten.

We hebben de accu laten testen: 97 procent capaciteit. Het rapport krijg je bij aflevering. Fabrieksgarantie op de accu loopt nog tot 2030.', 'available', 1, 301, NOW() - INTERVAL 2 DAY - INTERVAL 74 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 3, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Achteruitrijcamera', 'Draadloos laden', 'Keyless entry', 'Dodehoekdetectie', 'LED-koplampen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (3, 'ec37442689a1004deb2f9876b0996199.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (3, '0fdbf4b9ec7351e6691ce0e5149a2a63.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (3, 'd862df3ac36b7c68edd10bb212c290fe.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (3, 'b68f68ce8472c88a7f42eafe7df51635.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (3, '8439813f5373b8a71072ed4d5fa31bf2.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (4, 'peugeot-208-1-2-puretech-allure-2022', 4, '1.2 PureTech Allure', 'L334PR', 17950, 2022, '2022-04-22', 27300, 'petrol', 'manual', 'hatchback', 'Blauw metallic', 74, 1199, NULL, NULL, 5, 5, 1, '2027-10-15', 24, 'Compacte en vlotte 208 met het opvallende 3D-instrumentenpaneel. Lage kilometerstand en net een grote beurt gehad, inclusief vervanging van de distributieriem.

Perfect als eerste auto of als zuinige tweede auto voor de stad.', 'available', 0, 98, NOW() - INTERVAL 14 DAY - INTERVAL 111 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 4, id FROM features WHERE name IN ('Airconditioning', 'Cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren achter', 'Digitaal instrumentenpaneel', 'LED-koplampen', 'Lichtmetalen velgen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (4, 'faeab5d8ed396f0bab2b5ae218a0e51c.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (4, '1f65291c4cc8562f4a772fc88a978b6c.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (4, '1dc878faefd700abdaa8d89850817131.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (4, 'e43259d9194e6483bc803525ebe1f0e9.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (4, '36124bfc6a94b69d00331d927b45daa4.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (5, 'volvo-xc60-t6-recharge-awd-plus-bright-2022', 5, 'T6 Recharge AWD Plus Bright', 'R751GD', 46950, 2022, '2022-09-02', 46800, 'phev', 'automatic', 'suv', 'Zwart metallic', 251, 1969, NULL, NULL, 5, 5, 1, '2027-01-15', 24, 'Luxe en veilige plug-in hybride met vierwielaandrijving. Elektrisch rijd je in de praktijk zo’n 70 kilometer, genoeg voor de meeste woon-werkritten.

Deze auto is gereserveerd. Interesse in een vergelijkbare XC60? Laat het ons weten, dan zoeken we mee.', 'reserved', 1, 388, NOW() - INTERVAL 6 DAY - INTERVAL 148 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 5, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Lederen bekleding', 'Elektrisch verstelbare stoelen', 'Stoelverwarming', 'Panoramadak', 'Elektrische achterklep', '360°-camera', 'Head-up display', 'Navigatiesysteem', 'Trekhaak', 'Keyless entry', 'Dodehoekdetectie', 'Privacy glass');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (5, '1d8575c8941f83e8227681db13e6a875.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (5, 'e888e0449c15dd500c7b37177ec10db1.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (5, '1de26a871838b91a1fe732abe00c612c.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (5, '61746e2c1616d673ad671e13da15ce9d.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (5, 'f614a09380134266eac134e23876d19a.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (6, 'bmw-3-serie-touring-320d-high-executive-2019', 6, '320d High Executive', 'H128JX', 23450, 2019, '2019-02-09', 128500, 'diesel', 'automatic', 'estate', 'Donkerblauw', 140, 1995, NULL, NULL, 5, 5, 2, '2027-04-15', 12, 'Sportieve en zuinige diesel voor wie veel kilometers maakt. De 8-traps automaat schakelt onmerkbaar en de trekhaak trekt 1.800 kg.

De kilometerstand is volledig verklaarbaar via de NAP-registratie. Volledig onderhouden bij een merkdealer.', 'available', 0, 143, NOW() - INTERVAL 21 DAY - INTERVAL 185 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 6, id FROM features WHERE name IN ('Climate control', 'Cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Parkeersensoren voor', 'Parkeersensoren achter', 'Elektrische achterklep', 'Trekhaak', 'LED-koplampen', 'Lichtmetalen velgen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (6, '545ac45f7e867765a00120518fdf53e9.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (6, 'e8fd94268407fb024b3e488157fb00f0.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (6, '984cd44f19adb7e2981f0a3d55e3e218.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (6, '8663a00c0667b19cc46fcbed0cadd4ca.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (6, '29654d37f8ac70b6fa5d67daa6ba76cf.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (7, 'skoda-octavia-combi-1-5-tsi-style-2021', 7, '1.5 TSI Style', 'J563SF', 20950, 2021, '2021-07-16', 72100, 'petrol', 'manual', 'estate', 'Zilver metallic', 110, 1498, NULL, NULL, 5, 5, 1, '2027-07-15', 12, 'Enorm veel ruimte voor je geld. De Octavia Combi heeft een van de grootste kofferbakken in zijn klasse en rijdt comfortabel en stil.

Inclusief winterbanden op lichtmetalen velgen, die geven we er gratis bij.', 'available', 0, 121, NOW() - INTERVAL 11 DAY - INTERVAL 222 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 7, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Achteruitrijcamera', 'Stoelverwarming', 'Keyless entry', 'LED-koplampen', 'Dakrails', 'Isofix');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (7, 'f4ce221864725d1e0ff579b085236df0.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (7, 'c9a6b4c33a06f93a4f1437c93666bafd.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (7, '3cfa960aa41702e9d0ca9c9787129b87.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (7, '70241e2d41810e723f7562cab47b4295.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (7, '489a817181d57ebe16f9ddf5421c0c7b.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (8, 'renault-clio-1-0-tce-intens-2020', 8, '1.0 TCe Intens', 'G874VB', 13950, 2020, '2020-12-23', 61000, 'petrol', 'manual', 'hatchback', 'Rood', 74, 999, NULL, NULL, 5, 5, 2, '2027-10-15', 12, 'Zuinige en wendbare Clio met een goed uitgeruste Intens-uitvoering.

Deze auto is verkocht. Op zoek naar iets vergelijkbaars? Bekijk ons actuele aanbod.', 'sold', 0, 256, NOW() - INTERVAL 40 DAY - INTERVAL 259 MINUTE, NOW() - INTERVAL 3 DAY);
INSERT INTO car_features (car_id, feature_id) SELECT 8, id FROM features WHERE name IN ('Airconditioning', 'Cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Parkeersensoren achter', 'LED-koplampen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (8, 'dff1e6d56ded2a0228b263d5e5033948.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (8, '7af53b15998e79abe49fd95f0773c8ae.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (8, '9797fdba5c325b93f629c083909d7528.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (8, '69a9dd080044c3e3bebc9acdf1ad6e0c.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (8, 'e311786e8fa16295ff759e02cee62e49.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (9, 'mazda-mx-5-2-0-skyactiv-g-exclusive-line-2019', 9, '2.0 Skyactiv-G Exclusive-Line', 'S219KT', 24950, 2019, '2019-05-03', 39800, 'petrol', 'manual', 'convertible', 'Rood', 135, 1998, NULL, NULL, 2, 2, 1, '2027-01-15', 12, 'Puur rijplezier. De 2.0-liter motor met 184 pk en de korte handbak maken van elke rit een feestje. De softtop gaat in een paar seconden open.

Altijd binnen gestald en nooit in de winter gereden. Een echte liefhebbersauto.', 'available', 1, 342, NOW() - INTERVAL 5 DAY - INTERVAL 296 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 9, id FROM features WHERE name IN ('Climate control', 'Cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'LED-koplampen', 'Lichtmetalen velgen', 'Keyless entry');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (9, '74a4905b199f69353be30b1bf7e9ba20.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (9, 'cdbb39ef0d2221937d8c1857cf6ab47a.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (9, '674f931a9cd651bb3511e779b60c62e4.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (9, 'c96dc745628a3aad351eccf441b186f5.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (9, 'b45c718007ca2e848e867287bab1a306.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (10, 'ford-kuga-2-5-phev-st-line-x-2021', 10, '2.5 PHEV ST-Line X', 'T640RD', 27450, 2021, '2021-10-10', 69300, 'phev', 'automatic', 'suv', 'Antraciet', 165, 2488, NULL, NULL, 5, 5, 1, '2027-04-15', 12, 'Ruime SUV die je thuis aan de lader hangt. Zo’n 55 kilometer elektrisch bereik en daarna rijdt hij zuinig verder als hybride.

De ST-Line X is de best uitgeruste versie, met head-up display en een B&O-geluidssysteem.', 'available', 0, 154, NOW() - INTERVAL 17 DAY - INTERVAL 333 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 10, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Elektrische achterklep', 'Head-up display', 'Navigatiesysteem', 'Achteruitrijcamera', 'Draadloos laden', 'Trekhaak', 'Privacy glass');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (10, '5514b4301eff4ca892a99df2c84df629.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (10, '2330fce23b70276d2b46af0b023d6a44.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (10, 'f790873a81d1a1e243795d28a4523e05.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (10, 'd7ad691ec66c67c21dfb34a7280bb73a.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (10, 'dc47d78761359b69c861affaf645304d.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (11, 'hyundai-ioniq-5-77-kwh-connect-2022', 11, '77 kWh Connect', 'P385NX', 32950, 2022, '2022-03-17', 52600, 'electric', 'automatic', 'suv', 'Zilver metallic', 168, NULL, 77.4, 480, 5, 5, 1, '2027-07-15', 24, 'Futuristisch ontwerp en een van de snelst ladende elektrische auto’s: met 800 volt-techniek ga je in 18 minuten van 10 naar 80 procent.

Ruim interieur met een vlakke vloer en verschuifbare middenconsole. Accugezondheid getest: 96 procent.', 'available', 1, 412, NOW() - INTERVAL 1 DAY - INTERVAL 370 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 11, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', '360°-camera', 'Dodehoekdetectie', 'Head-up display', 'Draadloos laden', 'Keyless entry', 'LED-koplampen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (11, 'fe4a422fdbe592715eaade6c6a2d4058.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (11, '5d139b52dcd7974b8585a68bc7b22ecc.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (11, '342bd7cba2a43697fd311b49a0b47f56.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (11, '9dac4722982d7edc1c13869ad2e1338c.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (11, '9d47dd54cb88c62fa097dc9f4abc6ceb.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (12, 'volkswagen-touran-1-5-tsi-highline-7p-2020', 12, '1.5 TSI Highline 7p', 'H902LG', 24250, 2020, '2020-08-24', 89900, 'petrol', 'automatic', 'mpv', 'Parelwit', 110, 1498, NULL, NULL, 5, 7, 2, '2027-10-15', 12, 'Zeven zitplaatsen, drie losse Isofix-stoelen in de tweede rij en een vlakke laadvloer als je de achterste rij wegklapt. De ideale gezinsauto.

Rijdt stil en zuinig dankzij de 1.5 TSI met cilinderuitschakeling.', 'available', 0, 88, NOW() - INTERVAL 25 DAY - INTERVAL 407 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 12, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Parkeersensoren voor', 'Parkeersensoren achter', 'Achteruitrijcamera', 'Trekhaak', 'Isofix', 'Privacy glass');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (12, '04bc011da7f7cd95e841a0a39e3df6e3.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (12, 'ecfcf00af98c594cdb84d52abe8f38c6.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (12, '0e5649a21e288d068578bc82bec1e7c7.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (12, 'ce27780478082bfc9e6daef8978606ab.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (12, '171402cc883527190c837218016321b1.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (13, 'tesla-model-3-long-range-awd-2021', 13, 'Long Range AWD', 'L116ZP', 24950, 2021, '2021-01-04', 98400, 'electric', 'automatic', 'sedan', 'Wit', 324, NULL, 75, 560, 4, 5, 1, '2027-01-15', 12, 'Snelle en efficiënte elektrische sedan met vierwielaandrijving.

Deze auto is verkocht. Bekijk ons aanbod voor vergelijkbare elektrische auto’s.', 'sold', 0, 503, NOW() - INTERVAL 35 DAY - INTERVAL 444 MINUTE, NOW() - INTERVAL 9 DAY);
INSERT INTO car_features (car_id, feature_id) SELECT 13, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Panoramadak', 'Navigatiesysteem', 'Keyless entry', '360°-camera', 'Draadloos laden');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (13, 'dac0a37119411b018be26349849c6939.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (13, 'aceec3fd79f5dc04b08f470c276dfd8c.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (13, '0beda1071585d23c7f3112c72c7d4ae8.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (13, 'a5a80fdd3126f20a377001fe67050c53.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (13, 'e53254c61a58f0d9a8f46aff2b7ffde0.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (14, 'fiat-500-1-0-hybrid-dolcevita-2022', 14, '1.0 Hybrid Dolcevita', 'N447TF', 14450, 2022, '2022-06-11', 18200, 'hybrid', 'manual', 'hatchback', 'Mintgroen', 51, 999, NULL, NULL, 3, 4, 1, '2027-04-15', 24, 'Vrolijke stadsauto met een glazen dak en een heel lage kilometerstand. De mild-hybride motor helpt bij optrekken en houdt het verbruik laag.

Parkeren in de binnenstad wordt een stuk makkelijker.', 'available', 0, 176, NOW() - INTERVAL 8 DAY - INTERVAL 481 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 14, id FROM features WHERE name IN ('Airconditioning', 'Cruise control', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren achter', 'Panoramadak', 'Lichtmetalen velgen', 'DAB+ radio');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (14, 'a715272a7d406b1a4affe377aac24348.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (14, 'c4d6bf135863660bdbf1c741c3718915.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (14, '5f93d0ea698115b36f15e182ea51156b.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (14, 'd851e417f57e8758915637deebe7cd84.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (14, 'e07ec0ec15d93ddd949d9cbb3439ff1e.webp', 1600, 1200, 4);

INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (15, 'mercedes-benz-c-klasse-c-300-e-business-solution-amg-2022', 15, 'C 300 e Business Solution AMG', 'R279SB', 41950, 2022, '2022-11-18', 41700, 'phev', 'automatic', 'sedan', 'Zwart metallic', 230, 1999, NULL, NULL, 4, 5, 1, '2027-07-15', 24, 'Stijlvolle plug-in hybride met AMG-pakket. De nieuwe generatie haalt meer dan 100 kilometer elektrisch, dus veel ritten doe je zonder benzine.

Deze auto is gereserveerd voor een proefrit.', 'reserved', 0, 267, NOW() - INTERVAL 3 DAY - INTERVAL 518 MINUTE, NULL);
INSERT INTO car_features (car_id, feature_id) SELECT 15, id FROM features WHERE name IN ('Climate control', 'Adaptieve cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Achteruitrijcamera', 'Head-up display', 'Digitaal instrumentenpaneel', 'Keyless entry', 'LED-koplampen', 'Lichtmetalen velgen');
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (15, '71b93e70aeeb894339a77ee2a2e58bb7.webp', 1600, 1200, 0);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (15, 'b169440cb10907e94f2b4c3b2e8b844f.webp', 1600, 1200, 1);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (15, '5cdc3ef37cbe6e29bd0488583a679a8b.webp', 1600, 1200, 2);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (15, 'adbe88bf8a1ce65bfc044e8c3e690068.webp', 1600, 1200, 3);
INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (15, 'f07e709a533e3c5799b42679eb885dbb.webp', 1600, 1200, 4);


INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (1, 'RW-2026-0001', 'test_drive', 11, 'Hyundai IONIQ 5 77 kWh Connect', 'Lotte van Beers', 'lotte.vanbeers@example.com', '06 1111 2201', CURDATE() + INTERVAL 3 DAY, '10:30', 'Ik wil graag een proefrit maken. Kan ik ook een korte snelweg-rit doen om het geluid te testen?', 'new', NOW() - INTERVAL 0 DAY - INTERVAL 20 MINUTE, NOW() - INTERVAL 0 DAY - INTERVAL 20 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (1, NULL, NULL, 'new', NOW() - INTERVAL 0 DAY - INTERVAL 20 MINUTE);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (2, 'RW-2026-0002', 'question', 3, 'Kia Niro EV DynamicLine 64,8 kWh', 'Mehmet Yıldız', 'm.yildiz@example.com', '06 1111 2202', NULL, NULL, 'Is er een rapport van de accutest beschikbaar? En kan ik mijn huidige auto inruilen (Opel Corsa uit 2016)?', 'new', NOW() - INTERVAL 0 DAY - INTERVAL 73 MINUTE, NOW() - INTERVAL 0 DAY - INTERVAL 73 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (2, NULL, NULL, 'new', NOW() - INTERVAL 0 DAY - INTERVAL 73 MINUTE);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (3, 'RW-2026-0003', 'test_drive', 9, 'Mazda MX-5 2.0 Skyactiv-G Exclusive-Line', 'Joris Hendriks', 'joris.h@example.com', '', CURDATE() + INTERVAL 4 DAY, '14:00', '', 'new', NOW() - INTERVAL 1 DAY - INTERVAL 126 MINUTE, NOW() - INTERVAL 1 DAY - INTERVAL 126 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (3, NULL, NULL, 'new', NOW() - INTERVAL 1 DAY - INTERVAL 126 MINUTE);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (4, 'RW-2026-0004', 'test_drive', 5, 'Volvo XC60 T6 Recharge AWD Plus Bright', 'Fatima El Amrani', 'fatima.elamrani@example.com', '06 1111 2204', CURDATE() + INTERVAL 2 DAY, '11:30', 'Graag op zaterdag als dat lukt.', 'scheduled', NOW() - INTERVAL 2 DAY - INTERVAL 179 MINUTE, NOW() - INTERVAL 2 DAY - INTERVAL 179 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (4, NULL, NULL, 'new', NOW() - INTERVAL 2 DAY - INTERVAL 179 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (4, 1, 'new', 'scheduled', NOW() - INTERVAL 2 DAY - INTERVAL 179 MINUTE + INTERVAL 3 HOUR);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (5, 'RW-2026-0005', 'question', 1, 'Volkswagen Golf 1.5 eTSI Life Business', 'Ruud Verhagen', 'ruud.verhagen@example.com', '06 1111 2205', NULL, NULL, 'Wat zijn de kosten als ik de auto laat bezorgen in Tilburg?', 'contacted', NOW() - INTERVAL 3 DAY - INTERVAL 232 MINUTE, NOW() - INTERVAL 3 DAY - INTERVAL 232 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (5, NULL, NULL, 'new', NOW() - INTERVAL 3 DAY - INTERVAL 232 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (5, 1, 'new', 'contacted', NOW() - INTERVAL 3 DAY - INTERVAL 232 MINUTE + INTERVAL 3 HOUR);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (6, 'RW-2026-0006', 'test_drive', 15, 'Mercedes-Benz C-Klasse C 300 e Business Solution AMG', 'Sanne de Kort', 'sanne.dekort@example.com', '', CURDATE() + INTERVAL -2 DAY, '15:00', 'Ik ben vooral benieuwd naar het elektrische bereik.', 'completed', NOW() - INTERVAL 6 DAY - INTERVAL 285 MINUTE, NOW() - INTERVAL 6 DAY - INTERVAL 285 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (6, NULL, NULL, 'new', NOW() - INTERVAL 6 DAY - INTERVAL 285 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (6, 1, 'new', 'completed', NOW() - INTERVAL 6 DAY - INTERVAL 285 MINUTE + INTERVAL 3 HOUR);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (7, 'RW-2026-0007', 'question', 6, 'BMW 3 Serie Touring 320d High Executive', 'Pieter Swinkels', 'p.swinkels@example.com', '06 1111 2207', NULL, NULL, 'Zit er nog fabrieksgarantie op de automaat?', 'completed', NOW() - INTERVAL 9 DAY - INTERVAL 38 MINUTE, NOW() - INTERVAL 9 DAY - INTERVAL 38 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (7, NULL, NULL, 'new', NOW() - INTERVAL 9 DAY - INTERVAL 38 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (7, 1, 'new', 'completed', NOW() - INTERVAL 9 DAY - INTERVAL 38 MINUTE + INTERVAL 3 HOUR);
INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (8, 'RW-2026-0008', 'test_drive', 2, 'Toyota Corolla Touring Sports 1.8 Hybrid Active', 'Yara Bosman', 'yara.bosman@example.com', '06 1111 2208', CURDATE() + INTERVAL -6 DAY, '09:30', '', 'cancelled', NOW() - INTERVAL 12 DAY - INTERVAL 91 MINUTE, NOW() - INTERVAL 12 DAY - INTERVAL 91 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (8, NULL, NULL, 'new', NOW() - INTERVAL 12 DAY - INTERVAL 91 MINUTE);
INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (8, 1, 'new', 'cancelled', NOW() - INTERVAL 12 DAY - INTERVAL 91 MINUTE + INTERVAL 3 HOUR);
INSERT INTO request_notes (request_id, admin_id, body, created_at) VALUES (4, 1, 'Gebeld, komt zaterdag met partner. Auto klaarzetten en laden tot 100%.', NOW() - INTERVAL 1 DAY);
INSERT INTO request_notes (request_id, admin_id, body, created_at) VALUES (5, 1, 'Bezorgen binnen 50 km is gratis, offerte gemaild.', NOW() - INTERVAL 2 DAY);

INSERT INTO admins (id, name, email, password_hash) VALUES (1, 'Demo beheerder', 'demo@rijdwerk.test', '$2y$12$LFvu8y6Sn0WZkxiD7BlOs.fDIFRTU5gv24ZrRZyaYGf5bZe0OpdNy');
