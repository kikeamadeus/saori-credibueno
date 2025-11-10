import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../storage/token_storage.dart';

class AuthService {

  // =====================================================
  // LOGIN
  // =====================================================
  Future<Map<String, dynamic>> login(String username, String password) async {
    final url = Uri.parse("${AppConfig.baseUrl}/auth/login.php");

    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: json.encode({"username": username, "password": password}),
      );

      final data = json.decode(response.body);

      if (data['success'] == true) {
        final access = data['access_token'];
        final refresh = data['refresh_token'];
        final employeeId = data['employee']?['id'] ?? 0;
        final fullName = data['employee']?['full_name'] ?? '';

        // Guardar tokens
        await TokenStorage.saveTokens(access, refresh);

        // Guardar datos del usuario
        await TokenStorage.saveEmployeeId(employeeId);
        await TokenStorage.saveEmployeeName(fullName);

        return {"success": true, "employee": data['employee']};
      }

      return {
        "success": false,
        "message": data['message'] ?? "Credenciales inválidas"
      };

    } catch (_) {
      return {"success": false, "message": "Error de conexión"};
    }
  }

  // =====================================================
  // OBTENER PERFIL (requiere access_token válido)
  // =====================================================
  Future<Map<String, dynamic>> getProfile() async {
    final access = await TokenStorage.getAccessToken();
    if (access == null) return {"success": false};

    final url = Uri.parse("${AppConfig.baseUrl}/employees/profile.php");

    try {
      final response = await http.get(
        url,
        headers: {'Authorization': 'Bearer $access'},
      );

      final data = json.decode(response.body);

      if (data['success'] == true) {
        final employeeId = data['employee']?['id'] ?? 0;
        final fullName = data['employee']?['full_name'] ?? '';

        await TokenStorage.saveEmployeeId(employeeId);
        await TokenStorage.saveEmployeeName(fullName);

        return {"success": true, "employee": data['employee']};
      }

      return {"success": false};

    } catch (_) {
      return {"success": false};
    }
  }

  // =====================================================
  // VERIFICAR SESIÓN LOCAL
  // =====================================================
  Future<Map<String, dynamic>> checkStoredSession() async {
    final access = await TokenStorage.getAccessToken();
    final refresh = await TokenStorage.getRefreshToken();
    final id = await TokenStorage.getEmployeeId();

    // Si no hay nada, sesión inválida
    if (access == null || refresh == null || id == null) {
      return {"success": false, "message": "SESSION_NOT_FOUND"};
    }

    // Intentar validar perfil con access token
    final profile = await getProfile();
    if (profile['success'] == true) {
      return profile;
    }

    // Access expiró → intentar refresh
    final renewed = await refreshSession();
    if (renewed['success'] == true) {
      return renewed;
    }

    return {"success": false, "message": "SESSION_EXPIRED"};
  }

  // =====================================================
  // REFRESCAR TOKENS
  // =====================================================
  Future<Map<String, dynamic>> refreshSession() async {
    final refresh = await TokenStorage.getRefreshToken();
    if (refresh == null) return {"success": false};

    final url = Uri.parse("${AppConfig.baseUrl}/auth/refresh.php");

    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: json.encode({"refresh_token": refresh}),
      );

      final data = json.decode(response.body);

      if (data['success'] == true) {
        await TokenStorage.saveTokens(
          data['access_token'],
          data['refresh_token'],
        );

        final employeeId = data['employee']?['id'] ?? 0;
        final fullName = data['employee']?['full_name'] ?? '';

        await TokenStorage.saveEmployeeId(employeeId);
        await TokenStorage.saveEmployeeName(fullName);

        return {"success": true, "employee": data['employee']};
      }

      return {"success": false};

    } catch (_) {
      return {"success": false};
    }
  }

  // =====================================================
  // LOGOUT
  // =====================================================
  Future<void> logout() async {
    await TokenStorage.clear();
  }
}
