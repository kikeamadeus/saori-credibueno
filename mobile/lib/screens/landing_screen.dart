import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class LandingScreen extends StatelessWidget {
  const LandingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // ============================================================
      // FONDO GENERAL (color suave estilo SAORI–Credibueno)
      // ============================================================
      backgroundColor: const Color(0xFFEAF8F5),

      // ============================================================
      // APP BAR
      // ============================================================
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        title: Row(
          children: [
            Image.asset(
              'assets/images/credibueno_logo.png',
              height: 34,
            ),
          ],
        ),
        actions: [
          TextButton.icon(
            onPressed: () {
              Navigator.pushNamed(context, '/login');
            },
            icon: const Icon(Icons.login, color: Color(0xFF196273)),
            label: Text(
              "Iniciar sesión",
              style: GoogleFonts.outfit(
                color: const Color(0xFF196273),
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),

      // ============================================================
      // CONTENIDO PRINCIPAL
      // ============================================================
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 600),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const SizedBox(height: 20),

                // ====================================================
                // TÍTULO
                // ====================================================
                Text(
                  "Política de Puntualidad",
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(
                    fontSize: 26,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF196273),
                  ),
                ),

                const SizedBox(height: 30),

                // ====================================================
                // TEXTO PRINCIPAL
                // ====================================================
                Text(
                  "📌 Retardos semanales:\n"
                  "Si acumulas más de 15 minutos de retardos en una semana, se levanta un acta administrativa. "
                  "La sanción aplicable es un día de descuento.\n\n"
                  "📌 Reincidencias:\n"
                  "A partir de la 4ª acta, la sanción será de dos días. "
                  "Cada acta adicional suma un día extra.\n\n"
                  "Nuestro objetivo es fomentar la puntualidad y evitar sanciones. "
                  "Sabemos que pueden surgir imprevistos, por eso se maneja un margen de tolerancia.",
                  textAlign: TextAlign.justify,
                  style: GoogleFonts.outfit(
                    fontSize: 16,
                    height: 1.5,
                    fontWeight: FontWeight.w500,
                    color: const Color(0xFF196273),
                  ),
                ),

                const SizedBox(height: 40),

                // ====================================================
                // BOTÓN PRINCIPAL
                // ====================================================
                ElevatedButton(
                  onPressed: () {
                    Navigator.pushNamed(context, '/login');
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF196273),
                    padding: const EdgeInsets.symmetric(
                      horizontal: 32,
                      vertical: 14,
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    shadowColor: Colors.black.withOpacity(0.3),
                    elevation: 5,
                  ),
                  child: Text(
                    "ACEPTAR POLÍTICAS",
                    style: GoogleFonts.outfit(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                      letterSpacing: 1,
                    ),
                  ),
                ),

                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }
}