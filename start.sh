#!/bin/bash

# Start PHP built-in server in the background
php -S 0.0.0.0:${PORT:-3000} -t public/ &
SERVER_PID=$!

# Wait for a moment to ensure server starts
sleep 2

# Keep the script running
wait $SERVER_PID
