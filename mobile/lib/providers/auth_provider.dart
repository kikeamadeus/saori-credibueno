import 'package:flutter/foundation.dart';
import '../services/auth_service.dart';

class AuthProvider with ChangeNotifier {
  final AuthService _authService = AuthService();

  bool _isLoggedIn = false;

  int _employeeId = 0;
  String _employeeName = '';
  int _roleId = 0;
  Map<String, bool> _permissions = {};

  String _errorMessage = '';

  bool get isLoggedIn => _isLoggedIn;
  int get employeeId => _employeeId;
  String get employeeName => _employeeName;
  int get roleId => _roleId;
  Map<String, bool> get permissions => _permissions;
  String get errorMessage => _errorMessage;

  bool hasPermission(String key) {
    return _permissions[key] == true;
  }

  Future<bool> login(String user, String pass) async {
    final result = await _authService.login(user, pass);

    if (result['success'] == true) {
      final emp = result['employee'];

      _isLoggedIn = true;
      _employeeId = emp['id'];
      _employeeName = emp['full_name'];
      _roleId = emp['id_role'] ?? 0;
      _permissions = emp['permissions'] != null
          ? Map<String, bool>.from(emp['permissions'])
          : {};

      _errorMessage = '';
      notifyListeners();
      return true;
    }

    _isLoggedIn = false;
    _employeeId = 0;
    _employeeName = '';
    _roleId = 0;
    _permissions = {};
    _errorMessage = result['message'];

    notifyListeners();
    return false;
  }

  Future<void> checkStoredSession() async {
    final session = await _authService.checkStoredSession();

    if (session['success'] == true) {
      final emp = session['employee'];

      _isLoggedIn = true;
      _employeeId = emp['id'];
      _employeeName = emp['full_name'];
      _roleId = emp['id_role'] ?? 0;
      _permissions = emp['permissions'] != null
          ? Map<String, bool>.from(emp['permissions'])
          : {};

      _errorMessage = '';
    } else {
      _isLoggedIn = false;
      _employeeId = 0;
      _employeeName = '';
      _roleId = 0;
      _permissions = {};
      _errorMessage = session['message'];
    }

    notifyListeners();
  }

  Future<void> logout() async {
    await _authService.logout();

    _isLoggedIn = false;
    _employeeId = 0;
    _employeeName = '';
    _roleId = 0;
    _permissions = {};
    _errorMessage = '';

    notifyListeners();
  }
}