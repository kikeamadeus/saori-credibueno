# **Bitora**

**Bitora** es la base tecnológica de Arcobit: un micro–framework creado para desarrollar plataformas web, APIs y aplicaciones móviles de forma organizada, escalable y sin dependencias de frameworks externos.  
Su propósito es estandarizar la forma en que Arcobit construye software, evitando código repetido, acelerando el desarrollo y manteniendo la simplicidad como principio clave.

---

## ✅ ¿Qué es Bitora?
Bitora funciona como el núcleo de todos los proyectos futuros de Arcobit, incluyendo:

- **HACO** – Sistema de inventarios y gestión de productos.
- **SAORI** – Sistema de nómina y administración de recursos humanos.
- **DRAXLER** – Próximo módulo operativo/empresarial en desarrollo.

El objetivo de Bitora es que cada proyecto pueda construirse sobre la misma estructura lógica, compartiendo servicios clave como:
- Autenticación
- Control de usuarios y roles
- Conexión con base de datos
- Validación
- Estructura de API
- Seguridad básica
- Versionamiento

Todo esto sin depender de frameworks externos como Laravel, Symfony y similares, lo que garantiza total control sobre el código.

---

## ✅ Filosofía de desarrollo

1. **Simplicidad antes que complejidad**  
   Código claro, autodocumentado y fácil de mantener.

2. **Escalabilidad sin dolor**  
   Cada módulo puede crecer sin reescribir todo el sistema.

3. **Estandarización**  
   La misma estructura funciona para web, API y móvil.

---

## ✅ Tecnologías utilizadas
- **PHP 8+**
- **MariaDB / MySQL**
- **HTML, CSS, JavaScript**
- **JWT para autenticación**
- **Docker (ambiente de desarrollo)**
- **Flutter (interacción futura con API para aplicaciones móviles)**

---

## ✅ Estructura general (planeada)
bitora/
├── mobile/ # Aplicación móvil (Flutter)
│ ├── android/
│ ├── ios/
│ ├── lib/
│ ├── assets/
│ └── test/
│
├── platform/ # Infraestructura (Docker, config del entorno)
│ ├── docker-compose.yml
│ ├── Dockerfile
│ ├── logs/
│ └── php.ini
│
└── src/ # Core del framework Bitora
├── api/ # Endpoints REST
├── app/ # Lógica de negocio
├── config/ # Configuración general
├── helpers/ # Funciones auxiliares
├── layouts/ # Estructuras visuales HTML
├── middleware/ # Seguridad y validaciones
├── public/ # Archivos accesibles públicamente
├── services/ # Módulos reutilizables
├── shared/ # Clases y recursos compartidos
├── vendor/ # Dependencias (Composer)
├── .env # Variables de entorno
└── .env.development

## ✅ Objetivo a largo plazo
Convertir Bitora en un estándar interno de Arcobit para:

✅ Crear proyectos más rápido  
✅ Evitar código repetido  
✅ Habilitar integraciones con web, móvil y escritorio  
✅ Facilitar mantenimiento y soporte a múltiples clientes  
✅ Construir productos con versiones comerciales

---

## ✅ Estado actual
✔ Repositorio inicial creado  
✔ Docker configurado y funcionando con PHP + MariaDB  
✔ Preparado para convertirse en la base de HACO, DRAXLER y SAORI 