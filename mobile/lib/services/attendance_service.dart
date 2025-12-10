import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../storage/token_storage.dart';

class AttendanceService {
  /// ==========================================================
  /// REGISTRAR ASISTENCIA
  /// ==========================================================
  Future<Map<String, dynamic>> registerAttendance({
    required int employeeId,
    required double latitude,
    required double longitude,
  }) async {
    final accessToken = await TokenStorage.getAccessToken();

    if (accessToken == null) {
      return {
        "success": false,
        "message": "No hay sesión activa. Inicia sesión nuevamente.",
      };
    }

    final url = Uri.parse("${AppConfig.baseUrl}/attendance/create.php");

    try {
      final response = await http.post(
        url,
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer $accessToken",
        },
        body: json.encode({
          "employee_id": employeeId,
          "latitude": latitude,
          "longitude": longitude,
          "source": "mobile",
        }),
      );

      return json.decode(response.body);
    } catch (e) {
      return {
        "success": false,
        "message": "Error de conexión. Revisa tu internet.",
      };
    }
  }

  /// ==========================================================
  /// OBTENER ASISTENCIA DEL DÍA (EMPLEADO)
  /// ==========================================================
  Future<List<Map<String, dynamic>>> getTodayAttendance() async {
    final accessToken = await TokenStorage.getAccessToken();

    if (accessToken == null) {
      return [];
    }

    final url = Uri.parse(
      "${AppConfig.baseUrl}/attendance/get_today_attendance.php",
    );

    try {
      final response = await http.get(
        url,
        headers: {
          "Authorization": "Bearer $accessToken",
        },
      );

      final data = json.decode(response.body);

      if (data["success"] == true && data["data"] is List) {
        return List<Map<String, dynamic>>.from(data["data"]);
      }

      return [];
    } catch (e) {
      return [];
    }
  }
}