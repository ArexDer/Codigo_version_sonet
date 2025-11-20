import pandas as pd
import os

# Ruta del archivo Excel (usando raw string para evitar problemas con barras invertidas)
file_path = r'C:\BASECOTIZADORCODE\DOCUMENTACION\PASANTIAS_FINANMOTORS\COTIZADOR\Documentos a Usar\Precio_Inmobiliario_BASE.xlsx'

try:
    # Verificar si el archivo existe
    if not os.path.exists(file_path):
        print(f"❌ Error: No se encontró el archivo en la ruta: {file_path}")
        print("Verifica que la ruta y el nombre del archivo sean correctos.")
        exit(1)
    
    print("📂 Verificando archivo Excel...")
    
    # Primero, obtener todas las hojas disponibles
    excel_file = pd.ExcelFile(file_path)
    print(f"📋 Hojas disponibles en el Excel:")
    for i, sheet in enumerate(excel_file.sheet_names):
        print(f"  {i+1}. {sheet}")
    
    # Usar la primera hoja disponible (o puedes cambiar el índice)
    sheet_name = excel_file.sheet_names[0]
    print(f"🎯 Usando la hoja: '{sheet_name}'")
    
    # Cargar el archivo Excel desde el inicio (sin saltar filas)
    print("📊 Cargando datos...")
    df = pd.read_excel(file_path, sheet_name=sheet_name)
    #df = pd.read_excel(file_path, sheet_name=sheet_name, skiprows=2)
    
    # Imprimir información del DataFrame
    print("✅ Archivo cargado exitosamente!")
    print(f"📊 Dimensiones del DataFrame: {df.shape}")
    print("\n📋 Columnas originales del Excel:")
    for i, col in enumerate(df.columns):
        print(f"  {i+1}. {col}")
    
    print(f"\n🔢 Número de columnas: {len(df.columns)}")
    print(f"🔢 Número de filas: {len(df)}")
    
    # Verificar que hay suficientes columnas
    if len(df.columns) >= 9:
        # Seleccionar solo las primeras 9 columnas
        df = df.iloc[:, :9]
        
        # Renombrar las columnas
        df.columns = ['COD', 'MONTO', '60_MESES', '72_MESES', '84_MESES', '96_MESES', '108_MESES', '120_MESES', 'INSCRIPCION']
        print("\n✅ Columnas renombradas exitosamente:")
        for col in df.columns:
            print(f"  - {col}")
        
        # Limpiar datos: eliminar filas con valores nulos en columnas importantes
        df_clean = df.dropna(subset=['MONTO'])
        
        # Limpiar datos numéricos - manejar formato con comas decimales
        print("\n🧹 Limpiando formato de números...")
        numeric_columns = ['MONTO', '60_MESES', '72_MESES', '84_MESES', '96_MESES', '108_MESES', '120_MESES', 'INSCRIPCION']
        
        for col in numeric_columns:
            if col in df_clean.columns:
                # Convertir a string y limpiar formato
                df_clean[col] = df_clean[col].astype(str)
                df_clean[col] = df_clean[col].str.replace('$', '')  # Eliminar $
                df_clean[col] = df_clean[col].str.replace(' ', '')  # Eliminar espacios
                
                # Manejar números con comas como separador decimal (formato europeo)
                # Si tiene punto Y coma, es formato: 1,234.56
                # Si solo tiene coma, es formato: 123,45
                def clean_number(value):
                    try:
                        value = str(value).strip()
                        if value == 'nan' or value == '':
                            return None
                        
                        # Si tiene tanto punto como coma, formato americano: 1,234.56
                        if '.' in value and ',' in value:
                            # Eliminar comas (separadores de miles) y mantener punto decimal
                            value = value.replace(',', '')
                            return float(value)
                        
                        # Si solo tiene coma, es separador decimal europeo: 123,45
                        elif ',' in value and '.' not in value:
                            # Reemplazar coma con punto para convertir a float
                            value = value.replace(',', '.')
                            return float(value)
                        
                        # Si solo tiene punto o ninguno, formato normal
                        else:
                            return float(value)
                    except:
                        return None
                
                df_clean[col] = df_clean[col].apply(clean_number)
        
        # Eliminar filas donde MONTO sea NaN después de la limpieza
        df_clean = df_clean.dropna(subset=['MONTO'])
        print(f"🧹 Filas después de limpiar datos: {len(df_clean)}")
        
        # Mostrar una muestra de los datos
        print(f"\n👀 Muestra de datos limpiados (primeras 3 filas):")
        print(df_clean.head(3))
        
        # Convertir a JSON con formato legible
        json_data = df_clean.to_json(orient='records', indent=2)
        
        # Guardar el JSON
        output_file = 'inmobiliario_M2.json'
        with open(output_file, 'w', encoding='utf-8') as json_file:
            json_file.write(json_data)
        
        print(f"\n🎉 ¡Archivo JSON creado exitosamente!")
        print(f"📁 Archivo guardado como: {output_file}")
        print(f"📊 Total de registros exportados: {len(df_clean)}")
        
    else:
        print(f"❌ Error: El archivo solo tiene {len(df.columns)} columnas, pero se necesitan al menos 9.")
        print("Verifica la estructura del archivo Excel.")

except FileNotFoundError:
    print(f"❌ Error: No se pudo encontrar el archivo: {file_path}")
    print("Verifica que la ruta sea correcta y que el archivo exista.")
except PermissionError:
    print(f"❌ Error: Sin permisos para acceder al archivo: {file_path}")
    print("Verifica que el archivo no esté abierto en Excel u otra aplicación.")
except Exception as e:
    print(f"❌ Error inesperado: {e}")
    print("Detalles del error para depuración:")
    import traceback
    traceback.print_exc()

print("\n🔚 Proceso terminado.")
