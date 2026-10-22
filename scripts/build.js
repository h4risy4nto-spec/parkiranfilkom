import { execSync } from 'child_process';
import fs from 'fs';

// Jalankan build Vite
execSync('vite build', { stdio: 'inherit' });

// Pastikan folder dist ada agar kompatibel dengan Vercel static checks
if (!fs.existsSync('dist')) {
    fs.mkdirSync('dist', { recursive: true });
}
