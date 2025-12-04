import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../services/attendance_service.dart';
import '../helpers/location_helper.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter/material.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFEAF8F5),

      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        title: Row(
          children: [
            Image.asset('assets/images/credibueno_logo.png', height: 34),
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
      ),

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
            ),
          ],
        ),
      ),

      // ============================================================
      // BOTÓN FLOTANTE DE REGISTRO DE ASISTENCIA
      // ============================================================
      floatingActionButton: authProvider.hasPermission("register_attendance")
          ? FloatingActionButton(
              backgroundColor: const Color(0xFF196273),
              onPressed: () async {
                // 1) Obtener ubicación
                final pos = await LocationHelper.getCurrentPosition();

                if (pos == null) {
                  if (!context.mounted) return;
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: Colors.red,
                      content: Text(
                        "No se pudo obtener tu ubicación. Activa GPS.",
                        style: TextStyle(color: Colors.white),
                      ),
                    ),
                  );
                  return;
                }

                // 2) Registrar asistencia
                final service = AttendanceService();

                final result = await service.registerAttendance(
                  employeeId: authProvider.employeeId,
                  latitude: pos.latitude,
                  longitude: pos.longitude,
                );

                if (!context.mounted) return;

                // 3) Mostrar mensaje que viene del servidor
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    backgroundColor: result["success"]
                        ? Colors.green
                        : Colors.red,
                    content: Text(
                      result["message"] ?? "Sin mensaje",
                      style: const TextStyle(color: Colors.white),
                    ),
                    duration: const Duration(seconds: 3),
                  ),
                );
              },
              child: const Icon(
                Icons.access_time,
                size: 32,
                color: Colors.white,
              ),
            )
          : null,
    );
  }
}
