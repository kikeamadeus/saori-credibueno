import 'package:flutter/foundation.dart';
import '../services/auth_service.dart';

class AuthProvider with ChangeNotifier {
  final AuthService _authService = AuthService();

  bool _isLoggedIn = false;
  int _employeeId = 0;
  String _employeeName = '';
  String _errorMessage = '';

  bool get isLoggedIn => _isLoggedIn;
  int get employeeId => _employeeId;
  String get employeeName => _employeeName;
  String get errorMessage => _errorMessage;

  Future<bool> login(String user, String pass) async {
    final result = await _authService.login(user, pass);

    if (result['success'] == true) {
      final emp = result['employee'];
      _isLoggedIn = true;
      _employeeId = emp['id'];
      _employeeName = emp['full_name'];
      _errorMessage = '';
      notifyListeners();
      return true;
    }

    _isLoggedIn = false;
    _employeeId = 0;
    _employeeName = '';
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
      _errorMessage = '';
    } else {
      _isLoggedIn = false;
      _employeeId = 0;
      _employeeName = '';
      _errorMessage = session['message'];
    }

    notifyListeners();
  }

  Future<void> logout() async {
    await _authService.logout();
    _isLoggedIn = false;
    _employeeId = 0;
    _employeeName = '';
    _errorMessage = '';
    notifyListeners();
  }
}