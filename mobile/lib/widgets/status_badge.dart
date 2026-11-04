import 'package:flutter/material.dart';

class StatusBadge extends StatelessWidget {
  final String status;

  const StatusBadge({super.key, required this.status});

  @override
  Widget build(BuildContext context) {
    Color bg;
    Color text;
    String label;

    switch (status.toLowerCase()) {
      case 'new':
        bg = const Color(0xFF6366F1).withValues(alpha: 0.18);
        text = const Color(0xFF818CF8);
        label = 'New';
        break;
      case 'contacted':
        bg = const Color(0xFF06B6D4).withValues(alpha: 0.18);
        text = const Color(0xFF22D3EE);
        label = 'Contacted';
        break;
      case 'interested':
        bg = const Color(0xFF10B981).withValues(alpha: 0.18);
        text = const Color(0xFF34D399);
        label = 'Interested';
        break;
      case 'not_interested':
        bg = const Color(0xFFEF4444).withValues(alpha: 0.18);
        text = const Color(0xFFF87171);
        label = 'Not Interested';
        break;
      case 'closed':
        bg = const Color(0xFF64748B).withValues(alpha: 0.2);
        text = const Color(0xFF94A3B8);
        label = 'Closed';
        break;
      default:
        bg = Colors.grey.withValues(alpha: 0.15);
        text = Colors.grey;
        label = status;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: text,
          fontSize: 11,
          fontWeight: FontWeight.w700,
          letterSpacing: 0.3,
        ),
      ),
    );
  }
}
