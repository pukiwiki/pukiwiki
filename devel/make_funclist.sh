#!/bin/sh

grep -B1 "^function" ../*.php | sed -e "s/^\.\.\///" | sed "s/[a-z]*\.php\:function[ \t]*//"  
