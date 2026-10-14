const fs = require('fs');
const path = require('path');

const products = [
  { file: 'snake-plant.jpg', name: 'Snake Plant', pot: '#c86d51', leaf1: '#1b4332', leaf2: '#52b788', tag: 'Air Purifying' },
  { file: 'snake-plant-2.jpg', name: 'Snake Plant Rosette', pot: '#c86d51', leaf1: '#1b4332', leaf2: '#74c69d', tag: 'Dwarf Variety' },
  { file: 'peace-lily.jpg', name: 'Peace Lily', pot: '#2d6a4f', leaf1: '#0f271d', leaf2: '#ffffff', tag: 'Flowering & Purifier' },
  { file: 'peace-lily-2.jpg', name: 'Peace Lily White Bloom', pot: '#2d6a4f', leaf1: '#1b4332', leaf2: '#ffffff', tag: 'NASA Approved' },
  { file: 'money-plant.jpg', name: 'Money Plant', pot: '#e9c46a', leaf1: '#2d6a4f', leaf2: '#95d5b2', tag: 'Prosperity & Vastu' },
  { file: 'money-plant-2.jpg', name: 'Golden Money Leaves', pot: '#e9c46a', leaf1: '#40916c', leaf2: '#d8f3dc', tag: 'Trailing Vine' },
  { file: 'areca-palm.jpg', name: 'Areca Palm', pot: '#f4a261', leaf1: '#1b4332', leaf2: '#74c69d', tag: 'Natural Humidifier' },
  { file: 'zz-plant.jpg', name: 'ZZ Plant', pot: '#264653', leaf1: '#081c15', leaf2: '#2d6a4f', tag: 'Zero Maintenance' },
  { file: 'mogra.jpg', name: 'Arabian Mogra', pot: '#bc6c25', leaf1: '#2d6a4f', leaf2: '#fffbeb', tag: 'Fragrant Jasmine' },
  { file: 'mango-sapling.jpg', name: 'Alphonso Mango', pot: '#99582a', leaf1: '#1b4332', leaf2: '#f4a261', tag: 'Grafted Fruit Tree' },
  { file: 'desi-gulab.jpg', name: 'Desi Indian Rose', pot: '#c86d51', leaf1: '#1b4332', leaf2: '#e63946', tag: 'Traditional Fragrant' },
  { file: 'fiddle-leaf-fig.jpg', name: 'Fiddle Leaf Fig', pot: '#2b2d42', leaf1: '#1b4332', leaf2: '#52b788', tag: 'Designer Houseplant' },
  { file: 'jade-plant.jpg', name: 'Jade Succulent', pot: '#c86d51', leaf1: '#2d6a4f', leaf2: '#74c69d', tag: 'Feng Shui Luck' },
  { file: 'tulsi.jpg', name: 'Krishna Tulsi', pot: '#bc6c25', leaf1: '#3d1308', leaf2: '#52b788', tag: 'Sacred Ayurvedic Herb' },
  { file: 'plant-combo.jpg', name: 'Air Purifier Combo', pot: '#457b9d', leaf1: '#1b4332', leaf2: '#52b788', tag: '3-in-1 Green Bundle' },
  { file: 'seeds-kit.jpg', name: '12 Exotic Herb Seeds', pot: '#2a9d8f', leaf1: '#e76f51', leaf2: '#f4a261', tag: 'Organic Kitchen Kit' },
  { file: 'ceramic-planter.jpg', name: 'Ribbed Ceramic Pot', pot: '#1b4332', leaf1: '#d8f3dc', leaf2: '#52b788', tag: 'Glazed Artisanal' },
  { file: 'vermicompost.jpg', name: 'Vermicompost 5Kg', pot: '#33272a', leaf1: '#52b788', leaf2: '#74c69d', tag: '100% Organic Nutrition' },
  { file: 'pruning-shears.jpg', name: 'Japanese Pruner', pot: '#4a5568', leaf1: '#e2e8f0', leaf2: '#e63946', tag: 'SK5 Carbon Steel' },
  { file: 'neem-oil.jpg', name: 'Pure Neem Oil 250ml', pot: '#2d6a4f', leaf1: '#b7e4c7', leaf2: '#fefae0', tag: 'Organic Pest Control' },
  { file: 'potting-mix.jpg', name: 'Indoor Potting Mix 5Kg', pot: '#6c584c', leaf1: '#adc178', leaf2: '#dde5b6', tag: 'Sterilized Soilless' },
  { file: 'syngonium.jpg', name: 'Variegated Syngonium', pot: '#e76f51', leaf1: '#2d6a4f', leaf2: '#fefae0', tag: 'Pastel Foliage' },
  { file: 'bougainvillea.jpg', name: 'Dwarf Bougainvillea', pot: '#c86d51', leaf1: '#1b4332', leaf2: '#ff4d6d', tag: 'Balcony Sun Bloom' },
  { file: 'kagzi-lime.jpg', name: 'Kagzi Nimbu Lime', pot: '#99582a', leaf1: '#2d6a4f', leaf2: '#ffd166', tag: 'All-Year Juicy Fruit' },
  { file: 'monstera.jpg', name: 'Monstera Deliciosa', pot: '#1b4332', leaf1: '#0f271d', leaf2: '#52b788', tag: 'Swiss Cheese Splits' },
  { file: 'veggie-seeds.jpg', name: 'Desi Veggie Box', pot: '#264653', leaf1: '#2a9d8f', leaf2: '#e9c46a', tag: '8 Kitchen Essentials' },
  { file: 'owl-decor.jpg', name: 'Terracotta Owl Set', pot: '#e76f51', leaf1: '#f4a261', leaf2: '#2a9d8f', tag: 'Handmade Planter Decor' },
];

const dest = path.join(__dirname, '..', 'public', 'uploads', 'products');
if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });

for (const p of products) {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">
  <rect width="100%" height="100%" fill="#f5efe6" />
  <circle cx="250" cy="230" r="160" fill="#ede5d5" />
  <!-- Pot -->
  <path d="M170 310 L190 410 C190 416 196 420 204 420 L296 420 C304 420 310 416 310 410 L330 310 Z" fill="${p.pot}" />
  <rect x="160" y="300" width="180" height="16" rx="8" fill="${p.pot}" opacity="0.8" />
  <!-- Foliage -->
  <path d="M250 300 Q235 170 140 130 Q200 120 250 230 Q300 120 360 130 Q265 170 250 300 Z" fill="${p.leaf1}" />
  <path d="M250 230 Q240 110 180 70 Q230 70 250 160 Q270 70 320 70 Q260 110 250 230 Z" fill="${p.leaf2}" />
  <circle cx="250" cy="90" r="16" fill="${p.leaf2}" opacity="0.6" />
  <!-- Badge and Label -->
  <rect x="40" y="25" width="200" height="28" rx="14" fill="#1b4332" />
  <text x="140" y="44" text-anchor="middle" font-family="-apple-system, sans-serif" font-size="11" font-weight="bold" fill="#d8f3dc" letter-spacing="0.5">${p.tag.toUpperCase()}</text>
  <text x="250" y="460" text-anchor="middle" font-family="-apple-system, sans-serif" font-size="20" font-weight="bold" fill="#1b4332">${p.name}</text>
  <text x="250" y="482" text-anchor="middle" font-family="-apple-system, sans-serif" font-size="12" font-weight="600" fill="#7a897b">PlantPuglu Nursery Certified</text>
</svg>`;
  fs.writeFileSync(path.join(dest, p.file), svg, 'utf-8');
}

const bannerDest = path.join(__dirname, '..', 'public', 'uploads', 'banners');
if (!fs.existsSync(bannerDest)) fs.mkdirSync(bannerDest, { recursive: true });

const heroSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 500" width="600" height="500">
  <circle cx="300" cy="250" r="180" fill="#2d6a4f" opacity="0.4" />
  <path d="M200 340 L220 440 C220 446 226 450 234 450 L366 450 C374 450 380 446 380 440 L400 340 Z" fill="#c86d51" />
  <rect x="190" y="330" width="220" height="18" rx="9" fill="#a44a32" />
  <path d="M300 330 Q280 160 160 100 Q240 90 300 230 Q360 90 440 100 Q320 160 300 330 Z" fill="#52b788" />
  <path d="M300 230 Q290 80 220 30 Q280 30 300 140 Q320 30 380 30 Q310 80 300 230 Z" fill="#95d5b2" />
</svg>`;
fs.writeFileSync(path.join(bannerDest, 'hero-indoor-plants.jpg'), heroSvg, 'utf-8');
fs.writeFileSync(path.join(bannerDest, 'hero-flowering.jpg'), heroSvg, 'utf-8');
fs.writeFileSync(path.join(bannerDest, 'promo-combo.jpg'), heroSvg, 'utf-8');

console.log('Successfully generated all product and banner graphical assets.');
