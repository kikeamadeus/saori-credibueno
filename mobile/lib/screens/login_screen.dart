import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _userController = TextEditingController();
  final TextEditingController _passController = TextEditingController();
  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFE6FBF7), // Verde pastel clarito del web
      body: Center(
        child: SingleChildScrollView(
          child: Container(
            width: MediaQuery.of(context).size.width * 0.90,
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              boxShadow: const [
                BoxShadow(
                  color: Colors.black12,
                  blurRadius: 8,
                  offset: Offset(0, 2),
                )
              ],
            ),
            child: Column(
              children: [
                // LOGO
                SizedBox(
                  height: 90,
                  child: Image.asset(
                    "assets/images/unilogo.png", // Ajusta el nombre de tu logo
                    fit: BoxFit.contain,
                  ),
                ),

                const SizedBox(height: 20),

                // INPUT USUARIO
                TextField(
                  controller: _userController,
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(fontWeight: FontWeight.w600),
                  decoration: InputDecoration(
                    hintText: "Usuario",
                    hintStyle: GoogleFonts.outfit(
                        fontWeight: FontWeight.w500, color: Colors.black45),
                    contentPadding:
                        const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
                    enabledBorder: OutlineInputBorder(
                      borderSide: const BorderSide(
                          color: Color(0xFF196273), width: 2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: const BorderSide(
                          color: Color(0xFF196273), width: 2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                ),

                const SizedBox(height: 12),

                // INPUT CONTRASEÑA
                TextField(
                  controller: _passController,
                  obscureText: true,
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(fontWeight: FontWeight.w600),
                  decoration: InputDecoration(
                    hintText: "Contraseña",
                    hintStyle: GoogleFonts.outfit(
                        fontWeight: FontWeight.w500, color: Colors.black45),
                    contentPadding:
                        const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
                    enabledBorder: OutlineInputBorder(
                      borderSide: const BorderSide(
                          color: Color(0xFF196273), width: 2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: const BorderSide(
                          color: Color(0xFF196273), width: 2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                ),

                const SizedBox(height: 25),

                // BOTÓN
                _isLoading
                    ? const CircularProgressIndicator()
                    : SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF196273),
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(6),
                            ),
                            elevation: 3,
                          ),
                          onPressed: () async {
                            setState(() => _isLoading = true);

                            bool success = await authProvider.login(
                              _userController.text.trim(),
                              _passController.text.trim(),
                            );

                            setState(() => _isLoading = false);

                            if (!mounted) return;

                            if (success) {
                              Navigator.pushReplacementNamed(context, '/home');
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(
                                    authProvider.errorMessage,
                                    style: const TextStyle(color: Colors.white),
                                  ),
                                  backgroundColor: Colors.red,
                                ),
                              );
                            }
                          },
                          child: Text(
                            "INICIAR SESIÓN",
                            style: GoogleFonts.outfit(
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: Colors.white),
                          ),
                        ),
                      )
              ],
            ),
          ),
        ),
      ),
    );
  }
}