import json

data = {
    "$schema": "https://railway.app/railway.schema.json",
    "build": {
        "builder": "NIXPACKS"
    },
    "deploy": {
        "startCommand": "php artisan config:clear && php artisan cache:clear && php artisan storage:link --force && php artisan migrate --force && php artisan optimize && php artisan serve --host=0.0.0.0 --port=$PORT",
        "healthcheckPath": "/up",
        "restartPolicyType": "ON_FAILURE"
    }
}

with open("railway.json", "w", newline="\n") as f:
    json.dump(data, f, indent=2)

print("railway.json written successfully")
