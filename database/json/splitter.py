import json
import os

def split_json_file(input_file, output_prefix='json_part', max_per_file=1000):
    # Charger le contenu JSON
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
        

    # Vérifier que c’est bien une liste
    if not isinstance(data, list):
        raise ValueError("Le fichier JSON doit contenir une liste à la racine.")

    total = len(data)
    num_parts = (total + max_per_file - 1) // max_per_file  # ceil division

    for i in range(num_parts):
        part_data = data[i * max_per_file : (i + 1) * max_per_file]
        output_file = f"{output_prefix}{i+1}.json"
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(part_data, f, ensure_ascii=False, indent=2)
        print(f"{output_file} écrit avec {len(part_data)} éléments.")

# Exemple d'utilisation :
split_json_file("cities.json", output_prefix="cities_part", max_per_file=15000)
