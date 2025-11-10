import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (_, __) {
        debugPrint("Intento de ir atrás bloqueado");
      },
      child: Scaffold(
        appBar: AppBar(
          title: const Text("Panel principal"),
          automaticallyImplyLeading: false,
          actions: [
            IconButton(
              icon: const Icon(Icons.logout),
              onPressed: () async {
                await authProvider.logout();
                if (!context.mounted) return;
                Navigator.pushReplacementNamed(context, '/login');
              },
            ),
          ],
        ),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                "Bienvenido, ${authProvider.employeeName}",
                style: const TextStyle(fontSize: 20),
              ),
              const SizedBox(height: 8),
              Text(
                "Tu ID es: ${authProvider.employeeId}",
                style: const TextStyle(fontSize: 16),
              )
            ],
          ),
        ),
      ),
    );
  }
}