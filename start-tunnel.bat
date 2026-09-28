@echo off
title Cloudflare Tunnel - Parkiran FILKOM
echo ========================================================
echo   Menjalankan Cloudflare Tunnel untuk Parkiran FILKOM
echo   Pastikan Laravel server (artisan serve) sudah nyala!
echo ========================================================
echo.
cloudflared.exe tunnel --url http://127.0.0.1:8000
pause
