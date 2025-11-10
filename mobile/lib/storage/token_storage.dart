import 'package:shared_preferences/shared_preferences.dart';

class TokenStorage {
  // =====================================================
  // GUARDAR TOKENS
  // =====================================================
  static Future<void> saveTokens(String access, String refresh) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('access_token', access);
    await prefs.setString('refresh_token', refresh);
  }

  static Future<String?> getAccessToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('access_token');
  }

  static Future<String?> getRefreshToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('refresh_token');
  }

  // =====================================================
  // GUARDAR EMPLOYEE ID
  // =====================================================
  static Future<void> saveEmployeeId(int id) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('employee_id', id);
  }

  static Future<int?> getEmployeeId() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getInt('employee_id');
  }

  // =====================================================
  // GUARDAR EMPLOYEE NAME (opcional)
  // =====================================================
  static Future<void> saveEmployeeName(String name) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('employee_name', name);
  }

  static Future<String?> getEmployeeName() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('employee_name');
  }

  // =====================================================
  // BORRAR TODO (LOGOUT)
  // =====================================================
  static Future<void> clear() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('access_token');
    await prefs.remove('refresh_token');
    await prefs.remove('employee_id');
    await prefs.remove('employee_name');
  }
}
