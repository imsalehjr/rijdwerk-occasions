import { createHash } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { mkdirSync, writeFileSync, existsSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import sharp from 'sharp';
import { renders } from './car-render.mjs';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const uploadDir = join(root, 'public/uploads/cars');
mkdirSync(uploadDir, { recursive: true });

const COLORS = {
  'Grijs metallic': '#6b7177',
  'Wit': '#eceeef',
  'Parelwit': '#f1f1ec',
  'Zwart metallic': '#22252a',
  'Zilver metallic': '#b7bcc1',
  'Donkerblauw': '#1f3557',
  'Blauw metallic': '#2a5590',
  'Rood': '#a11f25',
  'Bosgroen': '#2e4d3d',
  'Mintgroen': '#9ccbb8',
  'Antraciet': '#3d4247',
};

const cars = [
  { brand: 'Volkswagen', model: 'Golf', variant: '1.5 eTSI Life Business', body: 'hatchback', fuel: 'petrol', transmission: 'automatic', year: 2021, km: 58400, price: 22950, color: 'Grijs metallic', kw: 110, cc: 1498, doors: 5, seats: 5, owners: 1, status: 'available', featured: 1, days: 4, views: 214, scale: 0.9,
    features: ['Adaptieve cruise control', 'Climate control', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren voor', 'Parkeersensoren achter', 'LED-koplampen', 'Lichtmetalen velgen', 'Digitaal instrumentenpaneel', 'Rijstrookassistent', 'Noodremassistent'],
    text: 'Een Golf zoals je hem wilt: één eigenaar, volledig bij de dealer onderhouden en met de soepele 7-traps DSG. De mild-hybride techniek maakt hem zuinig in de stad en stil op de snelweg.\n\nWe hebben hem in onze eigen werkplaats nagelopen: nieuwe remblokken voor, verse onderhoudsbeurt en een nieuwe APK. Klaar om weg te rijden.' },
  { brand: 'Toyota', model: 'Corolla Touring Sports', variant: '1.8 Hybrid Active', body: 'estate', fuel: 'hybrid', transmission: 'automatic', year: 2020, km: 84200, price: 19450, color: 'Wit', kw: 90, cc: 1798, doors: 5, seats: 5, owners: 1, status: 'available', featured: 0, days: 9, views: 167,
    features: ['Climate control', 'Adaptieve cruise control', 'Achteruitrijcamera', 'Apple CarPlay', 'Android Auto', 'Verkeersbordherkenning', 'Rijstrookassistent', 'DAB+ radio', 'Dakrails', 'Isofix'],
    text: 'Ruime, betrouwbare stationwagen met de bekende hybride aandrijving van Toyota. Rijdt in de stad een groot deel elektrisch en verbruikt in de praktijk rond 1 op 21.\n\nDe bagageruimte van ruim 580 liter maakt hem een ideale gezinsauto. Inclusief complete onderhoudshistorie.' },
  { brand: 'Kia', model: 'Niro EV', variant: 'DynamicLine 64,8 kWh', body: 'suv', fuel: 'electric', transmission: 'automatic', year: 2023, km: 31900, price: 27900, color: 'Bosgroen', kw: 150, battery: 64.8, range: 460, doors: 5, seats: 5, owners: 1, status: 'available', featured: 1, days: 2, views: 301, scale: 0.94,
    features: ['Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Achteruitrijcamera', 'Draadloos laden', 'Keyless entry', 'Dodehoekdetectie', 'LED-koplampen'],
    text: 'Volledig elektrisch met een echte actieradius van ruim 400 kilometer. Snelladen gaat van 10 naar 80 procent in ongeveer 45 minuten.\n\nWe hebben de accu laten testen: 97 procent capaciteit. Het rapport krijg je bij aflevering. Fabrieksgarantie op de accu loopt nog tot 2030.' },
  { brand: 'Peugeot', model: '208', variant: '1.2 PureTech Allure', body: 'hatchback', fuel: 'petrol', transmission: 'manual', year: 2022, km: 27300, price: 17950, color: 'Blauw metallic', kw: 74, cc: 1199, doors: 5, seats: 5, owners: 1, status: 'available', featured: 0, days: 14, views: 98, scale: 0.85,
    features: ['Airconditioning', 'Cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren achter', 'Digitaal instrumentenpaneel', 'LED-koplampen', 'Lichtmetalen velgen'],
    text: 'Compacte en vlotte 208 met het opvallende 3D-instrumentenpaneel. Lage kilometerstand en net een grote beurt gehad, inclusief vervanging van de distributieriem.\n\nPerfect als eerste auto of als zuinige tweede auto voor de stad.' },
  { brand: 'Volvo', model: 'XC60', variant: 'T6 Recharge AWD Plus Bright', body: 'suv', fuel: 'phev', transmission: 'automatic', year: 2022, km: 46800, price: 46950, color: 'Zwart metallic', kw: 251, cc: 1969, doors: 5, seats: 5, owners: 1, status: 'reserved', featured: 1, days: 6, views: 388, rim: 'dark', wheelStyle: 1,
    features: ['Climate control', 'Adaptieve cruise control', 'Lederen bekleding', 'Elektrisch verstelbare stoelen', 'Stoelverwarming', 'Panoramadak', 'Elektrische achterklep', '360°-camera', 'Head-up display', 'Navigatiesysteem', 'Trekhaak', 'Keyless entry', 'Dodehoekdetectie', 'Privacy glass'],
    text: 'Luxe en veilige plug-in hybride met vierwielaandrijving. Elektrisch rijd je in de praktijk zo’n 70 kilometer, genoeg voor de meeste woon-werkritten.\n\nDeze auto is gereserveerd. Interesse in een vergelijkbare XC60? Laat het ons weten, dan zoeken we mee.' },
  { brand: 'BMW', model: '3 Serie Touring', variant: '320d High Executive', body: 'estate', fuel: 'diesel', transmission: 'automatic', year: 2019, km: 128500, price: 23450, color: 'Donkerblauw', kw: 140, cc: 1995, doors: 5, seats: 5, owners: 2, status: 'available', featured: 0, days: 21, views: 143, wheelStyle: 1,
    features: ['Climate control', 'Cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Parkeersensoren voor', 'Parkeersensoren achter', 'Elektrische achterklep', 'Trekhaak', 'LED-koplampen', 'Lichtmetalen velgen'],
    text: 'Sportieve en zuinige diesel voor wie veel kilometers maakt. De 8-traps automaat schakelt onmerkbaar en de trekhaak trekt 1.800 kg.\n\nDe kilometerstand is volledig verklaarbaar via de NAP-registratie. Volledig onderhouden bij een merkdealer.' },
  { brand: 'Skoda', model: 'Octavia Combi', variant: '1.5 TSI Style', body: 'estate', fuel: 'petrol', transmission: 'manual', year: 2021, km: 72100, price: 20950, color: 'Zilver metallic', kw: 110, cc: 1498, doors: 5, seats: 5, owners: 1, status: 'available', featured: 0, days: 11, views: 121,
    features: ['Climate control', 'Adaptieve cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Achteruitrijcamera', 'Stoelverwarming', 'Keyless entry', 'LED-koplampen', 'Dakrails', 'Isofix'],
    text: 'Enorm veel ruimte voor je geld. De Octavia Combi heeft een van de grootste kofferbakken in zijn klasse en rijdt comfortabel en stil.\n\nInclusief winterbanden op lichtmetalen velgen, die geven we er gratis bij.' },
  { brand: 'Renault', model: 'Clio', variant: '1.0 TCe Intens', body: 'hatchback', fuel: 'petrol', transmission: 'manual', year: 2020, km: 61000, price: 13950, color: 'Rood', kw: 74, cc: 999, doors: 5, seats: 5, owners: 2, status: 'sold', featured: 0, days: 40, views: 256, scale: 0.84, soldDays: 3,
    features: ['Airconditioning', 'Cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Parkeersensoren achter', 'LED-koplampen'],
    text: 'Zuinige en wendbare Clio met een goed uitgeruste Intens-uitvoering.\n\nDeze auto is verkocht. Op zoek naar iets vergelijkbaars? Bekijk ons actuele aanbod.' },
  { brand: 'Mazda', model: 'MX-5', variant: '2.0 Skyactiv-G Exclusive-Line', body: 'convertible', fuel: 'petrol', transmission: 'manual', year: 2019, km: 39800, price: 24950, color: 'Rood', kw: 135, cc: 1998, doors: 2, seats: 2, owners: 1, status: 'available', featured: 1, days: 5, views: 342, scale: 0.84,
    features: ['Climate control', 'Cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'LED-koplampen', 'Lichtmetalen velgen', 'Keyless entry'],
    text: 'Puur rijplezier. De 2.0-liter motor met 184 pk en de korte handbak maken van elke rit een feestje. De softtop gaat in een paar seconden open.\n\nAltijd binnen gestald en nooit in de winter gereden. Een echte liefhebbersauto.' },
  { brand: 'Ford', model: 'Kuga', variant: '2.5 PHEV ST-Line X', body: 'suv', fuel: 'phev', transmission: 'automatic', year: 2021, km: 69300, price: 27450, color: 'Antraciet', kw: 165, cc: 2488, doors: 5, seats: 5, owners: 1, status: 'available', featured: 0, days: 17, views: 154, rim: 'dark',
    features: ['Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Elektrische achterklep', 'Head-up display', 'Navigatiesysteem', 'Achteruitrijcamera', 'Draadloos laden', 'Trekhaak', 'Privacy glass'],
    text: 'Ruime SUV die je thuis aan de lader hangt. Zo’n 55 kilometer elektrisch bereik en daarna rijdt hij zuinig verder als hybride.\n\nDe ST-Line X is de best uitgeruste versie, met head-up display en een B&O-geluidssysteem.' },
  { brand: 'Hyundai', model: 'IONIQ 5', variant: '77 kWh Connect', body: 'suv', fuel: 'electric', transmission: 'automatic', year: 2022, km: 52600, price: 32950, color: 'Zilver metallic', kw: 168, battery: 77.4, range: 480, doors: 5, seats: 5, owners: 1, status: 'available', featured: 1, days: 1, views: 412, scale: 1.0, wheelStyle: 1,
    features: ['Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Stuurverwarming', 'Navigatiesysteem', 'Apple CarPlay', 'Android Auto', '360°-camera', 'Dodehoekdetectie', 'Head-up display', 'Draadloos laden', 'Keyless entry', 'LED-koplampen'],
    text: 'Futuristisch ontwerp en een van de snelst ladende elektrische auto’s: met 800 volt-techniek ga je in 18 minuten van 10 naar 80 procent.\n\nRuim interieur met een vlakke vloer en verschuifbare middenconsole. Accugezondheid getest: 96 procent.' },
  { brand: 'Volkswagen', model: 'Touran', variant: '1.5 TSI Highline 7p', body: 'mpv', fuel: 'petrol', transmission: 'automatic', year: 2020, km: 89900, price: 24250, color: 'Parelwit', kw: 110, cc: 1498, doors: 5, seats: 7, owners: 2, status: 'available', featured: 0, days: 25, views: 88, scale: 0.95,
    features: ['Climate control', 'Adaptieve cruise control', 'Navigatiesysteem', 'Apple CarPlay', 'Parkeersensoren voor', 'Parkeersensoren achter', 'Achteruitrijcamera', 'Trekhaak', 'Isofix', 'Privacy glass'],
    text: 'Zeven zitplaatsen, drie losse Isofix-stoelen in de tweede rij en een vlakke laadvloer als je de achterste rij wegklapt. De ideale gezinsauto.\n\nRijdt stil en zuinig dankzij de 1.5 TSI met cilinderuitschakeling.' },
  { brand: 'Tesla', model: 'Model 3', variant: 'Long Range AWD', body: 'sedan', fuel: 'electric', transmission: 'automatic', year: 2021, km: 98400, price: 24950, color: 'Wit', kw: 324, battery: 75.0, range: 560, doors: 4, seats: 5, owners: 1, status: 'sold', featured: 0, days: 35, views: 503, rim: 'dark', soldDays: 9,
    features: ['Climate control', 'Adaptieve cruise control', 'Stoelverwarming', 'Panoramadak', 'Navigatiesysteem', 'Keyless entry', '360°-camera', 'Draadloos laden'],
    text: 'Snelle en efficiënte elektrische sedan met vierwielaandrijving.\n\nDeze auto is verkocht. Bekijk ons aanbod voor vergelijkbare elektrische auto’s.' },
  { brand: 'Fiat', model: '500', variant: '1.0 Hybrid Dolcevita', body: 'hatchback', fuel: 'hybrid', transmission: 'manual', year: 2022, km: 18200, price: 14450, color: 'Mintgroen', kw: 51, cc: 999, doors: 3, seats: 4, owners: 1, status: 'available', featured: 0, days: 8, views: 176, scale: 0.76,
    features: ['Airconditioning', 'Cruise control', 'Apple CarPlay', 'Android Auto', 'Parkeersensoren achter', 'Panoramadak', 'Lichtmetalen velgen', 'DAB+ radio'],
    text: 'Vrolijke stadsauto met een glazen dak en een heel lage kilometerstand. De mild-hybride motor helpt bij optrekken en houdt het verbruik laag.\n\nParkeren in de binnenstad wordt een stuk makkelijker.' },
  { brand: 'Mercedes-Benz', model: 'C-Klasse', variant: 'C 300 e Business Solution AMG', body: 'sedan', fuel: 'phev', transmission: 'automatic', year: 2022, km: 41700, price: 41950, color: 'Zwart metallic', kw: 230, cc: 1999, doors: 4, seats: 5, owners: 1, status: 'reserved', featured: 0, days: 3, views: 267, rim: 'dark', wheelStyle: 1,
    features: ['Climate control', 'Adaptieve cruise control', 'Lederen bekleding', 'Stoelverwarming', 'Navigatiesysteem', 'Achteruitrijcamera', 'Head-up display', 'Digitaal instrumentenpaneel', 'Keyless entry', 'LED-koplampen', 'Lichtmetalen velgen'],
    text: 'Stijlvolle plug-in hybride met AMG-pakket. De nieuwe generatie haalt meer dan 100 kilometer elektrisch, dus veel ritten doe je zonder benzine.\n\nDeze auto is gereserveerd voor een proefrit.' },
];


const slugify = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
const q = (s) => (s === null || s === undefined ? 'NULL' : `'${String(s).replace(/\\/g, '\\\\').replace(/'/g, "''")}'`);

const lines = ['USE rijdwerk;', '', 'SET FOREIGN_KEY_CHECKS = 0;', 'TRUNCATE request_status_log; TRUNCATE request_notes; TRUNCATE requests; TRUNCATE car_features; TRUNCATE car_photos; TRUNCATE cars; TRUNCATE models; TRUNCATE admins; TRUNCATE rate_limits; TRUNCATE rdw_cache;', 'SET FOREIGN_KEY_CHECKS = 1;', ''];

const models = new Map();
for (const c of cars) {
  const key = `${c.brand}|${c.model}`;
  if (!models.has(key)) models.set(key, models.size + 1);
}
for (const [key, id] of models) {
  const [brand, model] = key.split('|');
  lines.push(`INSERT INTO models (id, brand_id, name, slug) SELECT ${id}, id, ${q(model)}, ${q(slugify(model))} FROM brands WHERE name = ${q(brand)};`);
}
lines.push('');

const yearNow = 2026;
for (const [i, c] of cars.entries()) {
  const id = i + 1;
  const slug = slugify(`${c.brand} ${c.model} ${c.variant} ${c.year}`);
  const reg = `${c.year}-${String(((i * 5) % 12) + 1).padStart(2, '0')}-${String(((i * 7) % 27) + 1).padStart(2, '0')}`;
  const apk = `${yearNow + 1}-${String(((i * 3) % 12) + 1).padStart(2, '0')}-15`;
  lines.push(`INSERT INTO cars (id, slug, model_id, variant, plate, price, year, first_registration, mileage_km, fuel, transmission, body_type, color, power_kw, engine_cc, battery_kwh, range_km, doors, seats, owners, apk_until, warranty_months, description, status, is_featured, view_count, published_at, sold_at) VALUES (${id}, ${q(slug)}, ${models.get(`${c.brand}|${c.model}`)}, ${q(c.variant)}, NULL, ${c.price}, ${c.year}, ${q(reg)}, ${c.km}, ${q(c.fuel)}, ${q(c.transmission)}, ${q(c.body)}, ${q(c.color)}, ${c.kw}, ${c.cc ?? 'NULL'}, ${c.battery ?? 'NULL'}, ${c.range ?? 'NULL'}, ${c.doors}, ${c.seats}, ${c.owners}, ${q(apk)}, ${c.status === 'sold' ? 12 : (c.year >= 2022 ? 24 : 12)}, ${q(c.text)}, ${q(c.status)}, ${c.featured}, ${c.views}, NOW() - INTERVAL ${c.days} DAY - INTERVAL ${(i * 37) % 600} MINUTE, ${c.soldDays ? `NOW() - INTERVAL ${c.soldDays} DAY` : 'NULL'});`);
  lines.push(`INSERT INTO car_features (car_id, feature_id) SELECT ${id}, id FROM features WHERE name IN (${c.features.map(q).join(', ')});`);

  const svgs = renders({ ...c, color: COLORS[c.color] || '#777777', year: c.year });
  for (const [n, svg] of svgs.entries()) {
    const hash = createHash('md5').update(`rijdwerk-demo-${slug}-${n}`).digest('hex');
    const full = join(uploadDir, `${hash}.webp`);
    const thumb = join(uploadDir, `${hash}-640.webp`);
    if (!existsSync(full) || process.argv.includes('--force')) {
      const img = sharp(Buffer.from(svg), { density: 72 });
      await img.clone().resize(1600, 1200).webp({ quality: 82 }).toFile(full);
      await img.clone().resize(640, 480).webp({ quality: 78 }).toFile(thumb);
    }
    lines.push(`INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (${id}, '${hash}.webp', 1600, 1200, ${n});`);
  }
  lines.push('');
}

const people = [
  ['Lotte van Beers', 'lotte.vanbeers@example.com', '06 1111 2201'],
  ['Mehmet Yıldız', 'm.yildiz@example.com', '06 1111 2202'],
  ['Joris Hendriks', 'joris.h@example.com', ''],
  ['Fatima El Amrani', 'fatima.elamrani@example.com', '06 1111 2204'],
  ['Ruud Verhagen', 'ruud.verhagen@example.com', '06 1111 2205'],
  ['Sanne de Kort', 'sanne.dekort@example.com', ''],
  ['Pieter Swinkels', 'p.swinkels@example.com', '06 1111 2207'],
  ['Yara Bosman', 'yara.bosman@example.com', '06 1111 2208'],
];
const requests = [
  { type: 'test_drive', car: 11, p: 0, status: 'new', ago: 0, date: 3, time: '10:30', msg: 'Ik wil graag een proefrit maken. Kan ik ook een korte snelweg-rit doen om het geluid te testen?' },
  { type: 'question', car: 3, p: 1, status: 'new', ago: 0, msg: 'Is er een rapport van de accutest beschikbaar? En kan ik mijn huidige auto inruilen (Opel Corsa uit 2016)?' },
  { type: 'test_drive', car: 9, p: 2, status: 'new', ago: 1, date: 4, time: '14:00', msg: '' },
  { type: 'test_drive', car: 5, p: 3, status: 'scheduled', ago: 2, date: 2, time: '11:30', msg: 'Graag op zaterdag als dat lukt.' },
  { type: 'question', car: 1, p: 4, status: 'contacted', ago: 3, msg: 'Wat zijn de kosten als ik de auto laat bezorgen in Tilburg?' },
  { type: 'test_drive', car: 15, p: 5, status: 'completed', ago: 6, date: -2, time: '15:00', msg: 'Ik ben vooral benieuwd naar het elektrische bereik.' },
  { type: 'question', car: 6, p: 6, status: 'completed', ago: 9, msg: 'Zit er nog fabrieksgarantie op de automaat?' },
  { type: 'test_drive', car: 2, p: 7, status: 'cancelled', ago: 12, date: -6, time: '09:30', msg: '' },
];
lines.push('');
for (const [i, r] of requests.entries()) {
  const id = i + 1;
  const [name, email, phone] = people[r.p];
  const c = cars[r.car - 1];
  const label = `${c.brand} ${c.model} ${c.variant}`;
  const ref = `RW-2026-${String(id).padStart(4, '0')}`;
  const created = `NOW() - INTERVAL ${r.ago} DAY - INTERVAL ${(i * 53) % 300 + 20} MINUTE`;
  lines.push(`INSERT INTO requests (id, reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, status, consent_at, created_at) VALUES (${id}, ${q(ref)}, ${q(r.type)}, ${r.car}, ${q(label)}, ${q(name)}, ${q(email)}, ${q(phone)}, ${r.date !== undefined ? `CURDATE() + INTERVAL ${r.date} DAY` : 'NULL'}, ${r.time ? q(r.time) : 'NULL'}, ${q(r.msg)}, ${q(r.status)}, ${created}, ${created});`);
  lines.push(`INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (${id}, NULL, NULL, 'new', ${created});`);
  if (r.status !== 'new') {
    lines.push(`INSERT INTO request_status_log (request_id, admin_id, from_status, to_status, created_at) VALUES (${id}, 1, 'new', ${q(r.status)}, ${created} + INTERVAL 3 HOUR);`);
  }
}
lines.push(`INSERT INTO request_notes (request_id, admin_id, body, created_at) VALUES (4, 1, 'Gebeld, komt zaterdag met partner. Auto klaarzetten en laden tot 100%.', NOW() - INTERVAL 1 DAY);`);
lines.push(`INSERT INTO request_notes (request_id, admin_id, body, created_at) VALUES (5, 1, 'Bezorgen binnen 50 km is gratis, offerte gemaild.', NOW() - INTERVAL 2 DAY);`);
lines.push('');

const hash = execFileSync('php', ['-r', 'echo password_hash("RijdwerkDemo2026!", PASSWORD_BCRYPT, ["cost" => 12]);']).toString();
lines.push(`INSERT INTO admins (id, name, email, password_hash) VALUES (1, 'Demo beheerder', 'demo@rijdwerk.test', ${q(hash)});`);
lines.push('');

writeFileSync(join(root, 'database/demo.sql'), lines.join('\n'));
console.log(`demo.sql written: ${cars.length} cars, ${requests.length} requests`);
