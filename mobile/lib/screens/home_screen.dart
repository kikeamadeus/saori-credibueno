import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';

import '../providers/auth_provider.dart';
import '../services/attendance_service.dart';
import '../helpers/location_helper.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final AttendanceService _attendanceService = AttendanceService();

  bool _loading = true;
  List<Map<String, dynamic>> _attendanceList = [];

  // ==========================================================
  // CARGAR ASISTENCIA DEL DÍA
  // ==========================================================
  Future<void> _loadTodayAttendance() async {
    final data = await _attendanceService.getTodayAttendance();

    setState(() {
      _attendanceList = data;
      _loading = false;
    });
  }

  @override
  void initState() {
    super.initState();
    _loadTodayAttendance();
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return WillPopScope(
      // 🔒 Bloquear gesto y botón "atrás"
      onWillPop: () async => false,
      child: Scaffold(
        backgroundColor: const Color(0xFFEAF8F5),

        appBar: AppBar(
          backgroundColor: Colors.white,
          elevation: 1,
          automaticallyImplyLeading: false, // 🔒 Oculta flecha
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

        // ======================================================
        // BODY
        // ======================================================
        body: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                "Bienvenido(a): ${authProvider.employeeName}",
                style: GoogleFonts.outfit(
                  fontSize: 20,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xFF196273),
                ),
              ),

              const SizedBox(height: 16),

              Text(
                "Asistencia de hoy - Minutos de tolerencia: 3",
                style: GoogleFonts.outfit(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                ),
              ),

              const SizedBox(height: 8),

              Expanded(
                child: _loading
                    ? const Center(child: CircularProgressIndicator())
                    : _attendanceList.isEmpty
                        ? const Center(
                            child: Text(
                              "No hay registros de asistencia hoy.",
                              style: TextStyle(color: Colors.black54),
                            ),
                          )
                        : ListView.builder(
                            itemCount: _attendanceList.length,
                            itemBuilder: (context, index) {
                              final item = _attendanceList[index];

                              return Card(
                                elevation: 1,
                                margin:
                                    const EdgeInsets.symmetric(vertical: 6),
                                child: ListTile(
                                  leading: const Icon(
                                    Icons.access_time,
                                    color: Color(0xFF196273),
                                  ),
                                  title: Text(
                                    "${item['attendance_hour']} · ${item['attendance_type']}",
                                    style: GoogleFonts.outfit(
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  subtitle: Text(
                                    "Origen: ${item['source'].toString().toUpperCase()}",
                                  ),
                                ),
                              );
                            },
                          ),
              ),
            ],
          ),
        ),

        // ======================================================
        // BOTÓN REGISTRAR ASISTENCIA
        // ======================================================
        floatingActionButton: authProvider.hasPermission("register_attendance")
            ? FloatingActionButton(
                backgroundColor: const Color(0xFF196273),
                onPressed: () async {
                  final pos =
                      await LocationHelper.getCurrentPosition();

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

                  final result =
                      await _attendanceService.registerAttendance(
                    employeeId: authProvider.employeeId,
                    latitude: pos.latitude,
                    longitude: pos.longitude,
                  );

                  if (!context.mounted) return;

                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      backgroundColor: result["success"] == true
                          ? Colors.green
                          : Colors.red,
                      content: Text(
                        result["message"] ?? "Sin mensaje",
                        style:
                            const TextStyle(color: Colors.white),
                      ),
                    ),
                  );

                  // ✅ Recargar tabla si registró algo
                  if (result["success"] == true) {
                    setState(() {
                      _loading = true;
                    });
                    await _loadTodayAttendance();
                  }
                },
                child: const Icon(
                  Icons.access_time,
                  size: 32,
                  color: Colors.white,
                ),
              )
            : null,
      ),
    );
  }
}