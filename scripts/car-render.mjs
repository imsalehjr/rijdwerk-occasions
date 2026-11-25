const PROFILES = {
  hatchback: {
    h: 0.34, r: 0.077, axles: [0.19, 0.805], sill: 0.035,
    upper: [[0, 0.12], [0.004, 0.19], [0.06, 0.225], [0.27, 0.255, 1], [0.43, 0.335], [0.6, 0.342], [0.77, 0.33], [0.86, 0.31], [0.955, 0.24], [0.99, 0.17], [0.986, 0.07, 1]],
    glass: [[0.295, 0.247, 1], [0.44, 0.322], [0.6, 0.328], [0.76, 0.318], [0.845, 0.262, 1]],
    pillars: [0.5, 0.69], doors: [0.495, 0.69],
  },
  sedan: {
    h: 0.31, r: 0.072, axles: [0.175, 0.79], sill: 0.035,
    upper: [[0, 0.11], [0.004, 0.17], [0.07, 0.205], [0.29, 0.236, 1], [0.45, 0.31], [0.6, 0.313], [0.73, 0.292], [0.86, 0.242, 1], [0.975, 0.232], [1, 0.19], [0.996, 0.07, 1]],
    glass: [[0.31, 0.229, 1], [0.455, 0.298], [0.6, 0.301], [0.72, 0.284], [0.832, 0.237, 1]],
    pillars: [0.5, 0.71], doors: [0.495, 0.705],
  },
  estate: {
    h: 0.32, r: 0.072, axles: [0.175, 0.775], sill: 0.035,
    upper: [[0, 0.11], [0.004, 0.17], [0.07, 0.205], [0.29, 0.236, 1], [0.44, 0.315], [0.6, 0.322], [0.86, 0.312], [0.935, 0.29], [0.985, 0.222], [1, 0.17], [0.996, 0.07, 1]],
    glass: [[0.31, 0.229, 1], [0.45, 0.303], [0.6, 0.309], [0.845, 0.3], [0.925, 0.243, 1]],
    pillars: [0.49, 0.69, 0.84], doors: [0.485, 0.69],
  },
  suv: {
    h: 0.37, r: 0.083, axles: [0.19, 0.8], sill: 0.055,
    upper: [[0, 0.15], [0.004, 0.235], [0.07, 0.268], [0.28, 0.292, 1], [0.42, 0.362], [0.6, 0.372], [0.845, 0.362], [0.925, 0.345], [0.976, 0.282], [1, 0.212], [0.996, 0.09, 1]],
    glass: [[0.3, 0.287, 1], [0.43, 0.351], [0.6, 0.359], [0.832, 0.351], [0.912, 0.298, 1]],
    pillars: [0.495, 0.705, 0.83], doors: [0.49, 0.7],
  },
  mpv: {
    h: 0.37, r: 0.075, axles: [0.17, 0.785], sill: 0.045,
    upper: [[0, 0.14], [0.004, 0.212], [0.06, 0.247], [0.205, 0.272, 1], [0.385, 0.372], [0.6, 0.382], [0.88, 0.376], [0.95, 0.35], [0.99, 0.252], [1, 0.18], [0.996, 0.08, 1]],
    glass: [[0.225, 0.266, 1], [0.39, 0.362], [0.6, 0.37], [0.87, 0.364], [0.943, 0.302, 1]],
    pillars: [0.44, 0.66, 0.84], doors: [0.435, 0.66],
  },
  coupe: {
    h: 0.3, r: 0.074, axles: [0.18, 0.79], sill: 0.035,
    upper: [[0, 0.11], [0.004, 0.165], [0.07, 0.198], [0.31, 0.228, 1], [0.48, 0.3], [0.6, 0.302], [0.76, 0.27], [0.9, 0.228], [0.985, 0.212], [1, 0.18], [0.996, 0.07, 1]],
    glass: [[0.33, 0.222, 1], [0.48, 0.288], [0.6, 0.29], [0.74, 0.262], [0.83, 0.228, 1]],
    pillars: [0.6], doors: [0.6],
  },
  convertible: {
    h: 0.3, r: 0.074, axles: [0.2, 0.79], sill: 0.032,
    upper: [[0, 0.1], [0.004, 0.16], [0.08, 0.19], [0.36, 0.214, 1], [0.48, 0.226, 1], [0.74, 0.224], [0.95, 0.21], [1, 0.17], [0.996, 0.07, 1]],
    glass: null,
    windshield: [[0.355, 0.214], [0.445, 0.292], [0.46, 0.29], [0.385, 0.214]],
    pillars: [], doors: [0.58],
  },
};

const FRONT = {
  hatchback: { w: 0.86, h: 0.66, roofW: 0.64, belt: 0.6 },
  sedan: { w: 0.88, h: 0.6, roofW: 0.6, belt: 0.6 },
  estate: { w: 0.88, h: 0.62, roofW: 0.64, belt: 0.6 },
  suv: { w: 0.9, h: 0.74, roofW: 0.7, belt: 0.62 },
  mpv: { w: 0.86, h: 0.75, roofW: 0.74, belt: 0.56 },
  coupe: { w: 0.9, h: 0.56, roofW: 0.56, belt: 0.6 },
  convertible: { w: 0.86, h: 0.5, roofW: 0.0, belt: 0.64 },
};

function hex(c) {
  const n = parseInt(c.slice(1), 16);
  return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
}
function mix(a, b, t) {
  const A = hex(a), B = hex(b);
  return '#' + A.map((v, i) => Math.round(v + (B[i] - v) * t).toString(16).padStart(2, '0')).join('');
}

function smoothPath(pts, closed = false) {
  let d = `M${pts[0][0].toFixed(1)},${pts[0][1].toFixed(1)}`;
  for (let i = 0; i < pts.length - 1; i++) {
    const p0 = pts[i - 1] || pts[i], p1 = pts[i], p2 = pts[i + 1], p3 = pts[i + 2] || p2;
    if (p1[2] && p2[2]) {
      d += ` L${p2[0].toFixed(1)},${p2[1].toFixed(1)}`;
      continue;
    }
    const t = 0.18;
    const c1 = p1[2] ? [p1[0] + (p2[0] - p1[0]) * 0.3, p1[1] + (p2[1] - p1[1]) * 0.3] : [p1[0] + (p2[0] - p0[0]) * t, p1[1] + (p2[1] - p0[1]) * t];
    const c2 = p2[2] ? [p2[0] - (p2[0] - p1[0]) * 0.3, p2[1] - (p2[1] - p1[1]) * 0.3] : [p2[0] - (p3[0] - p1[0]) * t, p2[1] - (p3[1] - p1[1]) * t];
    d += ` C${c1[0].toFixed(1)},${c1[1].toFixed(1)} ${c2[0].toFixed(1)},${c2[1].toFixed(1)} ${p2[0].toFixed(1)},${p2[1].toFixed(1)}`;
  }
  return closed ? d + ' Z' : d;
}

function studio(id, tone = '#e9ecea') {
  return `
  <defs>
    <linearGradient id="${id}bg" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="${mix(tone, '#ffffff', 0.55)}"/>
      <stop offset="0.62" stop-color="${tone}"/>
      <stop offset="0.63" stop-color="${mix(tone, '#c9cfcd', 0.35)}"/>
      <stop offset="1" stop-color="${mix(tone, '#ffffff', 0.25)}"/>
    </linearGradient>
    <radialGradient id="${id}spot" cx="0.5" cy="0.42" r="0.6">
      <stop offset="0" stop-color="#ffffff" stop-opacity="0.75"/>
      <stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
    </radialGradient>
    <filter id="${id}blur" x="-20%" y="-50%" width="140%" height="200%"><feGaussianBlur stdDeviation="18"/></filter>
    <filter id="${id}blur2" x="-20%" y="-50%" width="140%" height="200%"><feGaussianBlur stdDeviation="5"/></filter>
  </defs>
  <rect width="1600" height="1200" fill="url(#${id}bg)"/>
  <rect width="1600" height="1200" fill="url(#${id}spot)"/>`;
}

function wheel(cx, cy, r, id, style = 0) {
  const spokes = [];
  const n = style === 1 ? 10 : 5;
  for (let i = 0; i < n; i++) {
    const a = (360 / n) * i + 18;
    const w = style === 1 ? r * 0.07 : r * 0.16;
    spokes.push(`<rect x="${(cx - w / 2).toFixed(1)}" y="${(cy - r * 0.66).toFixed(1)}" width="${w.toFixed(1)}" height="${(r * 0.5).toFixed(1)}" rx="${(w / 2).toFixed(1)}" fill="url(#${id}rim)" transform="rotate(${a} ${cx.toFixed(1)} ${cy.toFixed(1)})"/>`);
  }
  return `
  <circle cx="${cx}" cy="${cy}" r="${r}" fill="#17191b"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.97}" fill="none" stroke="#2c2f33" stroke-width="${r * 0.05}"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.7}" fill="#3a3e43"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.5}" fill="#5d6268"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.42}" fill="none" stroke="#454a50" stroke-width="${r * 0.03}"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.68}" fill="none" stroke="url(#${id}rim)" stroke-width="${r * 0.06}"/>
  ${spokes.join('')}
  <circle cx="${cx}" cy="${cy}" r="${r * 0.15}" fill="url(#${id}rim)"/>
  <circle cx="${cx}" cy="${cy}" r="${r * 0.06}" fill="#2a2d31"/>`;
}

export function sideView(car, opts = {}) {
  const P = PROFILES[car.body];
  const id = 's' + (opts.idSuffix || '');
  const scale = car.scale || 1;
  const len = 1260 * scale;
  const x0 = 800 - len / 2;
  const ground = 862;
  const X = (x) => x0 + x * len;
  const Y = (y) => ground - y * len;
  const r = P.r * len;
  const [fa, ra] = P.axles.map(X);
  const cy = ground - r;
  const arch = r * 1.14;
  const base = car.color;
  const light = mix(base, '#ffffff', 0.42);
  const dark = mix(base, '#000000', 0.5);

  const upper = P.upper.map(([x, y, s]) => [X(x), Y(y), s]);
  let d = smoothPath(upper);
  const sillY = Y(P.sill);
  const archPath = (cx) => {
    const dx = Math.sqrt(Math.max(arch * arch - (sillY - cy) ** 2, 0));
    return ` L${(cx + dx).toFixed(1)},${sillY.toFixed(1)} A${arch.toFixed(1)},${arch.toFixed(1)} 0 0 0 ${(cx - dx).toFixed(1)},${sillY.toFixed(1)}`;
  };
  d += ` L${X(0.975).toFixed(1)},${sillY.toFixed(1)}`;
  d += archPath(ra);
  d += archPath(fa);
  d += ` L${X(0.025).toFixed(1)},${sillY.toFixed(1)} Z`;

  const beltY = Y((P.glass ? P.glass[0][1] : P.upper[3][1]) - 0.012);
  let glass = '';
  if (P.glass) {
    const g = P.glass.map(([x, y, s]) => [X(x), Y(y), s]);
    const gp = smoothPath(g, true);
    const pillars = P.pillars.map((px) => `<rect x="${(X(px) - len * 0.008).toFixed(1)}" y="${Y(0.4).toFixed(1)}" width="${(len * 0.016).toFixed(1)}" height="${(len * 0.2).toFixed(1)}" fill="#141619"/>`).join('');
    glass = `
    <clipPath id="${id}gclip"><path d="${gp}"/></clipPath>
    <path d="${gp}" fill="url(#${id}glass)"/>
    <g clip-path="url(#${id}gclip)">
      <path d="M${X(0.3)},${Y(0.2)} L${X(0.5)},${Y(0.4)} L${X(0.56)},${Y(0.4)} L${X(0.36)},${Y(0.2)} Z" fill="#ffffff" opacity="0.12"/>
      <path d="M${X(0.62)},${Y(0.2)} L${X(0.8)},${Y(0.4)} L${X(0.83)},${Y(0.4)} L${X(0.65)},${Y(0.2)} Z" fill="#ffffff" opacity="0.08"/>
      ${pillars}
    </g>
    <path d="${gp}" fill="none" stroke="#0e1012" stroke-width="5"/>`;
  } else if (P.windshield) {
    const w = P.windshield.map(([x, y]) => [X(x), Y(y), 1]);
    glass = `<path d="${smoothPath(w, true)}" fill="url(#${id}glass)" stroke="#121417" stroke-width="4"/>
      <path d="M${X(0.5)},${Y(0.226)} C${X(0.52)},${Y(0.27)} ${X(0.56)},${Y(0.272)} ${X(0.58)},${Y(0.226)} Z" fill="#1b1d20"/>
      <path d="M${X(0.6)},${Y(0.226)} C${X(0.62)},${Y(0.268)} ${X(0.655)},${Y(0.268)} ${X(0.675)},${Y(0.226)} Z" fill="#1b1d20"/>
      <path d="M${X(0.47)},${Y(0.226)} L${X(0.73)},${Y(0.224)}" stroke="#101214" stroke-width="7" stroke-linecap="round"/>`;
  }

  const doors = P.doors.map((dx) => `<path d="M${X(dx)},${beltY + 4} C${X(dx) + 6},${Y(0.15)} ${X(dx) + 6},${Y(0.1)} ${X(dx) - 2},${sillY - 8}" stroke="${dark}" stroke-opacity="0.55" stroke-width="2.4" fill="none"/>`).join('');
  const handles = P.doors.map((dx) => `<rect x="${X(dx) - len * 0.05}" y="${beltY + len * 0.022}" width="${len * 0.032}" height="${len * 0.0075}" rx="4" fill="${dark}" opacity="0.7"/>`).join('');
  const frontLast = P.upper[2];
  const rearTail = P.upper[P.upper.length - 2];

  const head = `<path d="M${X(0.008)},${Y(frontLast[1] - 0.032)} Q${X(0.04)},${Y(frontLast[1] - 0.014)} ${X(0.078)},${Y(frontLast[1] - 0.006)} L${X(0.074)},${Y(frontLast[1] - 0.02)} Q${X(0.04)},${Y(frontLast[1] - 0.032)} ${X(0.012)},${Y(frontLast[1] - 0.046)} Z" fill="#dfe6ec" stroke="#23272b" stroke-width="2.5"/>
    <path d="M${X(0.014)},${Y(frontLast[1] - 0.04)} Q${X(0.04)},${Y(frontLast[1] - 0.026)} ${X(0.07)},${Y(frontLast[1] - 0.016)}" stroke="#ffffff" stroke-width="3" fill="none"/>`;
  const tail = `<path d="M${X(rearTail[0] - 0.055)},${Y(rearTail[1] + 0.005)} L${X(rearTail[0] - 0.002)},${Y(rearTail[1] - 0.004)} L${X(rearTail[0] - 0.006)},${Y(rearTail[1] - 0.03)} L${X(rearTail[0] - 0.05)},${Y(rearTail[1] - 0.018)} Z" fill="#a3161d" stroke="#3b0a0d" stroke-width="2"/>`;

  const mirrorX = X(P.glass ? P.glass[0][0] + 0.012 : 0.39);
  const mirror = car.body === 'convertible' ? '' : `<path d="M${mirrorX},${beltY - 2} q${len * 0.012},-${len * 0.03} ${len * 0.04},-${len * 0.028} l2,${len * 0.022} q-${len * 0.012},${len * 0.012} -${len * 0.042},${len * 0.006} Z" fill="url(#${id}paint)" stroke="${dark}" stroke-width="2"/>`;

  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${opts.viewBox || '0 0 1600 1200'}" width="${opts.width || 1600}" height="${opts.height || 1200}">
  ${studio(id, car.backdrop)}
  <defs>
    <linearGradient id="${id}paint" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="${light}"/>
      <stop offset="0.35" stop-color="${base}"/>
      <stop offset="0.62" stop-color="${mix(base, '#000000', 0.12)}"/>
      <stop offset="0.66" stop-color="${mix(base, '#ffffff', 0.18)}"/>
      <stop offset="1" stop-color="${dark}"/>
    </linearGradient>
    <linearGradient id="${id}glass" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#3c4856"/>
      <stop offset="1" stop-color="#151a20"/>
    </linearGradient>
    <linearGradient id="${id}rim" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="${car.rim === 'dark' ? '#5a5f66' : '#f2f4f5'}"/>
      <stop offset="1" stop-color="${car.rim === 'dark' ? '#25282c' : '#8d949b'}"/>
    </linearGradient>
    <clipPath id="${id}body"><path d="${d}"/></clipPath>
    <linearGradient id="${id}fade" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#fff" stop-opacity="0.22"/>
      <stop offset="0.35" stop-color="#fff" stop-opacity="0"/>
    </linearGradient>
    <mask id="${id}refl" maskUnits="userSpaceOnUse" x="0" y="${ground}" width="1600" height="${1200 - ground}"><rect x="0" y="${ground}" width="1600" height="${1200 - ground}" fill="url(#${id}fade)"/></mask>
  </defs>
  <use href="#${id}car" transform="translate(0 ${2 * ground}) scale(1 -1)" mask="url(#${id}refl)"/>
  <ellipse cx="800" cy="${ground + 6}" rx="${len * 0.56}" ry="34" fill="#1c2024" opacity="0.38" filter="url(#${id}blur)"/>
  <ellipse cx="${fa}" cy="${ground}" rx="${r * 0.95}" ry="10" fill="#0c0e10" opacity="0.55" filter="url(#${id}blur2)"/>
  <ellipse cx="${ra}" cy="${ground}" rx="${r * 0.95}" ry="10" fill="#0c0e10" opacity="0.55" filter="url(#${id}blur2)"/>
  <g id="${id}car">
  <circle cx="${fa}" cy="${cy}" r="${arch}" fill="#0f1113"/>
  <circle cx="${ra}" cy="${cy}" r="${arch}" fill="#0f1113"/>
  <path d="${d}" fill="url(#${id}paint)"/>
  <g clip-path="url(#${id}body)">
    <rect x="0" y="${sillY - len * 0.03}" width="1600" height="${len * 0.05}" fill="#16181b" opacity="0.85"/>
    <path d="M0,${beltY + len * 0.012} L1600,${beltY + len * 0.004}" stroke="${mix(base, '#ffffff', 0.55)}" stroke-opacity="0.55" stroke-width="3"/>
    <rect x="0" y="${Y(0.21)}" width="1600" height="${len * 0.02}" fill="#ffffff" opacity="0.07"/>
  </g>
  ${glass}
  ${doors}
  ${handles}
  ${mirror}
  ${head}
  ${tail}
  ${wheel(fa, cy, r, id, car.wheelStyle || 0)}
  ${wheel(ra, cy, r, id, car.wheelStyle || 0)}
  </g>
</svg>`;
}

function plate(cx, cy, w, label = 'RIJDWERK') {
  const h = w * 0.22;
  return `<g>
    <rect x="${cx - w / 2}" y="${cy - h / 2}" width="${w}" height="${h}" rx="${h * 0.12}" fill="#f5c518" stroke="#1d1f22" stroke-width="3"/>
    <rect x="${cx - w / 2 + 2}" y="${cy - h / 2 + 2}" width="${w * 0.09}" height="${h - 4}" rx="${h * 0.08}" fill="#1d3f99"/>
    <text x="${cx + w * 0.045}" y="${cy + h * 0.27}" font-family="DejaVu Sans Condensed, Arial Narrow, sans-serif" font-weight="700" font-size="${h * 0.72}" text-anchor="middle" fill="#121416" letter-spacing="2">${label}</text>
  </g>`;
}

export function frontView(car, rear = false) {
  const F = FRONT[car.body];
  const id = rear ? 'r' : 'f';
  const W = 900 * F.w * (car.scale ? 0.92 + car.scale * 0.08 : 1);
  const H = 900 * F.h * (car.scale ? car.scale : 1);
  const cx = 800, ground = 900;
  const top = ground - H;
  const beltY = ground - H * F.belt;
  const base = car.color, light = mix(base, '#ffffff', 0.4), dark = mix(base, '#000000', 0.5);
  const tireW = W * 0.12, tireH = H * 0.2;
  const roofW = W * F.roofW;
  const bodyPath = `M${cx - W / 2},${ground - tireH * 0.55}
    L${cx - W / 2},${beltY + 30} Q${cx - W / 2},${beltY} ${cx - W / 2 + 40},${beltY - 6}
    L${cx + W / 2 - 40},${beltY - 6} Q${cx + W / 2},${beltY} ${cx + W / 2},${beltY + 30}
    L${cx + W / 2},${ground - tireH * 0.55} Q${cx + W / 2},${ground - tireH * 0.3} ${cx + W / 2 - 30},${ground - tireH * 0.3}
    L${cx - W / 2 + 30},${ground - tireH * 0.3} Q${cx - W / 2},${ground - tireH * 0.3} ${cx - W / 2},${ground - tireH * 0.55} Z`;
  const cabin = F.roofW > 0
    ? `<path d="M${cx - W / 2 + 46},${beltY - 4} L${cx - roofW / 2},${top + 14} Q${cx - roofW / 2 + 6},${top} ${cx - roofW / 2 + 30},${top} L${cx + roofW / 2 - 30},${top} Q${cx + roofW / 2 - 6},${top} ${cx + roofW / 2},${top + 14} L${cx + W / 2 - 46},${beltY - 4} Z" fill="url(#${id}paint)"/>
       <path d="M${cx - W / 2 + 70},${beltY - 10} L${cx - roofW / 2 + 16},${top + 24} L${cx + roofW / 2 - 16},${top + 24} L${cx + W / 2 - 70},${beltY - 10} Z" fill="url(#${id}glass)"/>
       <path d="M${cx - roofW / 2 + 40},${top + 30} L${cx - roofW / 2 + 120},${top + 30} L${cx - W / 2 + 200},${beltY - 14} L${cx - W / 2 + 120},${beltY - 14} Z" fill="#ffffff" opacity="0.1"/>`
    : `<path d="M${cx - W / 2 + 80},${beltY - 6} L${cx - W / 2 + 130},${beltY - H * 0.28} L${cx + W / 2 - 130},${beltY - H * 0.28} L${cx + W / 2 - 80},${beltY - 6} Z" fill="url(#${id}glass)" stroke="#121417" stroke-width="5"/>`;
  const lights = rear
    ? `<path d="M${cx - W / 2 + 14},${beltY + 22} L${cx - W * 0.2},${beltY + 26} L${cx - W * 0.2},${beltY + 52} L${cx - W / 2 + 16},${beltY + 58} Z" fill="#a3161d" stroke="#3b0a0d" stroke-width="3"/>
       <path d="M${cx + W / 2 - 14},${beltY + 22} L${cx + W * 0.2},${beltY + 26} L${cx + W * 0.2},${beltY + 52} L${cx + W / 2 - 16},${beltY + 58} Z" fill="#a3161d" stroke="#3b0a0d" stroke-width="3"/>
       <rect x="${cx - W * 0.2}" y="${beltY + 34}" width="${W * 0.4}" height="8" fill="#6d0f13"/>`
    : `<path d="M${cx - W / 2 + 16},${beltY + 26} L${cx - W * 0.17},${beltY + 34} L${cx - W * 0.18},${beltY + 56} L${cx - W / 2 + 20},${beltY + 54} Z" fill="#e9eef2" stroke="#2a2e33" stroke-width="3"/>
       <path d="M${cx + W / 2 - 16},${beltY + 26} L${cx + W * 0.17},${beltY + 34} L${cx + W * 0.18},${beltY + 56} L${cx + W / 2 - 20},${beltY + 54} Z" fill="#e9eef2" stroke="#2a2e33" stroke-width="3"/>
       <path d="M${cx - W / 2 + 24},${beltY + 32} L${cx - W * 0.18},${beltY + 40}" stroke="#ffffff" stroke-width="4"/>
       <path d="M${cx + W / 2 - 24},${beltY + 32} L${cx + W * 0.18},${beltY + 40}" stroke="#ffffff" stroke-width="4"/>
       <path d="M${cx - W * 0.15},${beltY + 40} L${cx + W * 0.15},${beltY + 40} L${cx + W * 0.13},${beltY + 78} L${cx - W * 0.13},${beltY + 78} Z" fill="#191b1e"/>
       <circle cx="${cx}" cy="${beltY + 58}" r="13" fill="none" stroke="#c9ced3" stroke-width="4"/>`;
  const intake = rear
    ? `<rect x="${cx - W * 0.42}" y="${ground - tireH * 0.62}" width="${W * 0.84}" height="${tireH * 0.24}" rx="12" fill="#1b1d20"/>`
    : `<path d="M${cx - W * 0.3},${ground - tireH * 0.75} L${cx + W * 0.3},${ground - tireH * 0.75} L${cx + W * 0.26},${ground - tireH * 0.42} L${cx - W * 0.26},${ground - tireH * 0.42} Z" fill="#1b1d20"/>`;
  const mirrors = car.body === 'convertible' ? '' : `<path d="M${cx - W / 2 + 20},${beltY - 10} l-70,-6 q-12,0 -10,14 l4,22 q2,8 12,6 l64,-12 Z" fill="url(#${id}paint)" stroke="${dark}" stroke-width="2"/>
    <path d="M${cx + W / 2 - 20},${beltY - 10} l70,-6 q12,0 10,14 l-4,22 q-2,8 -12,6 l-64,-12 Z" fill="url(#${id}paint)" stroke="${dark}" stroke-width="2"/>`;
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 1200" width="1600" height="1200">
  ${studio(id, car.backdrop)}
  <defs>
    <linearGradient id="${id}paint" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0" stop-color="${dark}"/>
      <stop offset="0.18" stop-color="${base}"/>
      <stop offset="0.45" stop-color="${light}"/>
      <stop offset="0.6" stop-color="${base}"/>
      <stop offset="1" stop-color="${dark}"/>
    </linearGradient>
    <linearGradient id="${id}glass" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#46525f"/>
      <stop offset="1" stop-color="#14181d"/>
    </linearGradient>
  </defs>
  <ellipse cx="${cx}" cy="${ground + 4}" rx="${W * 0.62}" ry="34" fill="#1c2024" opacity="0.4" filter="url(#${id}blur)"/>
  <rect x="${cx - W / 2 + 8}" y="${ground - tireH}" width="${tireW}" height="${tireH}" rx="16" fill="#17191b"/>
  <rect x="${cx + W / 2 - 8 - tireW}" y="${ground - tireH}" width="${tireW}" height="${tireH}" rx="16" fill="#17191b"/>
  ${cabin}
  <path d="${bodyPath}" fill="url(#${id}paint)"/>
  ${lights}
  ${intake}
  ${plate(cx, ground - tireH * (rear ? 1.05 : 1.05), W * (rear ? 0.32 : 0.3))}
  ${mirrors}
</svg>`;
}

export function interiorView(car) {
  const accent = car.interior || '#2a2d31';
  const screen = car.year >= 2021 ? 1 : 0.72;
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 1200" width="1600" height="1200">
  <defs>
    <linearGradient id="isky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#dfe6ea"/>
      <stop offset="1" stop-color="#f6f7f6"/>
    </linearGradient>
    <linearGradient id="idash" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="${mix(accent, '#ffffff', 0.12)}"/>
      <stop offset="1" stop-color="${mix(accent, '#000000', 0.45)}"/>
    </linearGradient>
    <linearGradient id="iscreen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#1d2a36"/>
      <stop offset="1" stop-color="#0d1218"/>
    </linearGradient>
    <filter id="iblur"><feGaussianBlur stdDeviation="10"/></filter>
  </defs>
  <rect width="1600" height="1200" fill="url(#isky)"/>
  <path d="M0,520 Q400,470 800,480 Q1200,470 1600,520 L1600,560 L0,560 Z" fill="#c9d2cf" opacity="0.6" filter="url(#iblur)"/>
  <path d="M0,0 L230,0 L60,700 L0,700 Z" fill="#202326"/>
  <path d="M1600,0 L1370,0 L1540,700 L1600,700 Z" fill="#202326"/>
  <path d="M0,0 L1600,0 L1600,60 Q800,110 0,60 Z" fill="#1a1c1f"/>
  <path d="M0,600 Q800,520 1600,600 L1600,1200 L0,1200 Z" fill="url(#idash)"/>
  <path d="M0,640 Q800,560 1600,640" stroke="${mix(accent, '#ffffff', 0.3)}" stroke-width="4" fill="none" opacity="0.5"/>
  <rect x="${800 - 230 * screen}" y="560" width="${460 * screen}" height="${270 * screen}" rx="22" fill="url(#iscreen)" stroke="#050607" stroke-width="6"/>
  <g opacity="0.9">
    <path d="M${800 - 190 * screen},${560 + 200 * screen} C${800 - 80 * screen},${560 + 120 * screen} ${800 + 20 * screen},${560 + 190 * screen} ${800 + 180 * screen},${560 + 70 * screen}" stroke="#7fb0d8" stroke-width="7" fill="none" stroke-linecap="round"/>
    <circle cx="${800 + 180 * screen}" cy="${560 + 70 * screen}" r="12" fill="#e7a04b"/>
    <rect x="${800 - 200 * screen}" y="${560 + 24 * screen}" width="${130 * screen}" height="14" rx="7" fill="#3a4b5b"/>
  </g>
  <rect x="660" y="890" width="280" height="70" rx="18" fill="#17191b"/>
  <circle cx="720" cy="925" r="18" fill="#2b2f34"/><circle cx="800" cy="925" r="18" fill="#2b2f34"/><circle cx="880" cy="925" r="18" fill="#2b2f34"/>
  <rect x="300" y="640" width="260" height="110" rx="20" fill="#0e1114"/>
  <path d="M330,720 A80,80 0 0 1 470,690" stroke="#c9ced3" stroke-width="6" fill="none"/>
  <circle cx="430" cy="1010" r="300" fill="none" stroke="#141618" stroke-width="58"/>
  <path d="M190,1010 Q430,880 670,1010" stroke="#141618" stroke-width="70" fill="none"/>
  <circle cx="430" cy="1010" r="86" fill="#1d2023"/>
  <circle cx="430" cy="1010" r="30" fill="#2c3035"/>
  <rect x="1050" y="660" width="320" height="26" rx="13" fill="#141618" opacity="0.8"/>
  <rect x="1080" y="700" width="120" height="44" rx="10" fill="#141618" opacity="0.8"/>
</svg>`;
}

export function renders(car) {
  const fwX = 800 - (1260 * (car.scale || 1)) / 2 + PROFILES[car.body].axles[0] * 1260 * (car.scale || 1);
  return [
    sideView(car),
    frontView(car, false),
    frontView(car, true),
    sideView(car, { idSuffix: 'z', viewBox: `${(fwX - 330).toFixed(0)} 480 660 495` }),
    interiorView(car),
  ];
}
