import 'package:flutter/material.dart';
import '../config/app_config.dart';

class SecondaryPhoneDialog extends StatefulWidget {
  final String leadName;
  final String? initialPhone;
  final Function(String phone) onSave;

  const SecondaryPhoneDialog({
    super.key,
    required this.leadName,
    this.initialPhone,
    required this.onSave,
  });

  @override
  State<SecondaryPhoneDialog> createState() => _SecondaryPhoneDialogState();
}

class _SecondaryPhoneDialogState extends State<SecondaryPhoneDialog> {
  late final TextEditingController _controller;

  @override
  void initState() {
    super.initState();
    _controller = TextEditingController(text: widget.initialPhone ?? '');
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      backgroundColor: AppConfig.surfaceColor,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: Text(
        'Secondary Phone Number',
        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
      ),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            widget.leadName,
            style: const TextStyle(
              fontSize: 13,
              color: AppConfig.textSecondary,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _controller,
            keyboardType: TextInputType.phone,
            autofocus: true,
            style: const TextStyle(color: Colors.white),
            decoration: InputDecoration(
              prefixIcon: const Icon(Icons.phone_iphone, color: AppConfig.primaryColor, size: 20),
              hintText: 'e.g. +1 (555) 019-2834',
              labelText: 'Alternate Number',
            ),
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Cancel', style: TextStyle(color: AppConfig.textSecondary)),
        ),
        ElevatedButton(
          style: ElevatedButton.styleFrom(
            backgroundColor: AppConfig.primaryColor,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
          onPressed: () {
            widget.onSave(_controller.text.trim());
            Navigator.pop(context);
          },
          child: const Text('Save Number'),
        ),
      ],
    );
  }
}
