# Technical Debt – SAORI / Módulo: Sucursales

### **Fecha:** 2025-11-17 12:40

### **Tema:**  
Sincronización de domicilio con coordendas (latitud y logitud)

### **Estado:**  
- No implementado

### **Descripción del desarrollo:**  
Se busca implementar un GeoCoding ya sea con una API de Google Maps o un servicio gratituido para que al editar o crear una nueva sucusal, escribiendo su domicilio se pueda automáticamente implementar la latitud y longitud de esa dirección.
Se debe recordar que el mapa no puede establacer especificamente el marcador, como desarrollador si buscas precisión podrías escribir las coordenadas en la base de datos directamente.

### **Impacto:**   
- Bajo  

### **Razón / Decisión:**  
Actualmente la empresa Credibueno no requiere de dar de alta, baja o editar sucursales ya que tiene más de 5 años sin registrar una nueva sucursal, además de que las circunstancias presentes llevan a que cuando se busque abrir una nueva sucursal existiría un protoco de alta especifico que dará tiempo para implementar este desarrollo.

---

### **Notas adicionales (opcional):**  
El impacto de esta deuda técnica es bajo debido a que el sistema puede funcionar con la alta de las sucursales actuales sin perder continuidad en los demás módulos.

---