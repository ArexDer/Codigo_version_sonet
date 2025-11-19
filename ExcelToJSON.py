import pandas as pd

# Lee el archivo Excel y especifica la hoja
file_path = 'C:\BASECOTIZADORCODE\DOCUMENTACION\PASANTIAS_FINANMOTORS\COTIZADOR\Documentos a Usar\Precios_Inmobiliario_BASE.xlsx'  # Cambia esto a la ruta de tu archivo

sheet_name = 'Hoja1'  # Nombre de la hoja que mencionas

# Cargar el archivo Excel a partir de la fila 19
df = pd.read_excel(file_path, sheet_name=sheet_name, skiprows=2)

# Imprimir los nombres originales de las columnas
print("Columnas originales del Excel:")
print(df.columns)

# Imprimir el número de columnas
print(f"Número de columnas: {len(df.columns)}")

# Si las columnas coinciden, renómbralas
if len(df.columns) >= 9:  # Asegúrate de que hay suficientes columnas para renombrar
    df = df.iloc[:, :9]  # Solo selecciona las primeras 9 columnas
    df.columns = ['COD', 'MONTO', '24_MESES', '36_MESES', '48_MESES', '60_MESES', '72_MESES','84_MESES', 'INSCRIPCION']
else:
    print("El archivo no tiene suficientes columnas para renombrar.")

# Convertir el DataFrame a formato JSON
json_data = df.to_json(orient='records', indent=4)

# Guardar el JSON en un archivo
with open('inmobiliarior_M2.json', 'w') as json_file:
    json_file.write(json_data)

print("Archivo JSON creado exitosamente.")
