import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (_, __) {
        debugPrint("Intento de ir atrás bloqueado");
      },
      child: Scaffold(
        // ============================================================
        // FONDO GENERAL – Color suave de Credibueno
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
              const SizedBox(width: 10),
              Text(
                "Panel principal",
                style: GoogleFonts.outfit(
                  color: const Color(0xFF196273),
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),

          automaticallyImplyLeading: false,
          actions: [
            IconButton(
              icon: const Icon(Icons.logout, color: Color(0xFF196273)),
              onPressed: () async {
                await authProvider.logout();
                if (!context.mounted) return;
                Navigator.pushReplacementNamed(context, '/login');
              },
            ),
          ],
        ),

        // ============================================================
        // CUERPO
        // ============================================================
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                "Bienvenido, ${authProvider.employeeName}",
                style: GoogleFonts.outfit(
                  fontSize: 20,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xFF196273),
                ),
              ),
              const SizedBox(height: 8),
              Text(
                "Tu ID es: ${authProvider.employeeId}",
                style: GoogleFonts.outfit(
                  fontSize: 16,
                  fontWeight: FontWeight.w500,
                  color: Colors.black54,
                ),
              )
            ],
          ),
        ),

        // ============================================================
        // BOTÓN FLOTANTE
        // ============================================================
        floatingActionButton: FloatingActionButton(
          backgroundColor: const Color(0xFF196273),
          onPressed: () {
            debugPrint("Se presionó el botón +");
          },
          child: const Icon(Icons.add, size: 32, color: Colors.white),
        ),
      ),
    );
  }
}