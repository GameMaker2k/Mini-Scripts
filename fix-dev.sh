#!/bin/bash
for dev in null zero full random urandom tty; do
    case $dev in
        null)    major=1 minor=3 ;;
        zero)    major=1 minor=5 ;;
        full)    major=1 minor=7 ;;
        random)  major=1 minor=8 ;;
        urandom) major=1 minor=9 ;;
        tty)     major=5 minor=0 ;;
    esac
    [ -e /dev/$dev ] || sudo mknod -m 666 /dev/$dev c $major $minor
done
