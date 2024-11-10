#!/bin/bash

# Ensure the .env.example exists before copying
if [ -f ./backend/.env.example ]; then
  cp ./backend/.env.example ./backend/.env
  echo ".env file created successfully."
else
  echo ".env.example file not found."
fi
