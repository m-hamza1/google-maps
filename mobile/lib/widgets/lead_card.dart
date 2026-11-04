import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../config/app_config.dart';
import '../models/lead.dart';
import 'secondary_phone_dialog.dart';
import 'status_badge.dart';

class LeadCard extends StatelessWidget {
  final Lead lead;
  final VoidCallback onTap;
  final Function(String secondaryPhone) onSaveSecondaryPhone;

  const LeadCard({
    super.key,
    required this.lead,
    required this.onTap,
    required this.onSaveSecondaryPhone,
  });

  Future<void> _makeCall(String phone) async {
    final cleanDigits = phone.replaceAll(RegExp(r'[^\d+]'), '');
    final uri = Uri.parse('tel:$cleanDigits');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Top Row: Name and Status Badge
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Text(
                      lead.name,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: AppConfig.textPrimary,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8),
                  StatusBadge(status: lead.status),
                ],
              ),
              const SizedBox(height: 8),

              // Category & Rating Row
              Row(
                children: [
                  if (lead.city != null) ...[
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppConfig.primaryColor.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.location_on, size: 10, color: AppConfig.primaryColor),
                          const SizedBox(width: 3),
                          Text(
                            lead.city!,
                            style: const TextStyle(
                              fontSize: 11,
                              color: AppConfig.primaryColor,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 8),
                  ],
                  if (lead.category != null) ...[
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppConfig.surfaceElevated,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        lead.category!,
                        style: const TextStyle(
                          fontSize: 11,
                          color: AppConfig.textSecondary,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                  ],
                  if (lead.rating != null) ...[
                    Row(
                      children: [
                        const Icon(Icons.star, color: Colors.amber, size: 14),
                        const SizedBox(width: 3),
                        Text(
                          lead.rating!.toStringAsFixed(1),
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            color: Colors.amber,
                          ),
                        ),
                        if (lead.reviewsCount > 0)
                          Text(
                            ' (${lead.reviewsCount})',
                            style: const TextStyle(
                              fontSize: 11,
                              color: AppConfig.textSecondary,
                            ),
                          ),
                      ],
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 10),

              // Address
              if (lead.address != null) ...[
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.location_on_outlined, size: 14, color: AppConfig.textSecondary),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        lead.address!,
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppConfig.textSecondary,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
              ],

              // Divider
              const Divider(color: Color(0xFF334155), height: 1),
              const SizedBox(height: 10),

              // Phones Action Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Primary Phone
                  if (lead.hasPhone)
                    InkWell(
                      onTap: () => _makeCall(lead.primaryPhone!),
                      borderRadius: BorderRadius.circular(8),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 6),
                        child: Row(
                          children: [
                            const Icon(Icons.phone, size: 14, color: AppConfig.secondaryColor),
                            const SizedBox(width: 6),
                            Text(
                              lead.primaryPhone!,
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppConfig.secondaryColor,
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    const Text(
                      'No Primary Phone',
                      style: TextStyle(fontSize: 12, color: Colors.grey),
                    ),

                  // Secondary Phone Action
                  if (lead.hasSecondaryPhone)
                    InkWell(
                      onTap: () => _showEditSecondaryDialog(context),
                      borderRadius: BorderRadius.circular(8),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppConfig.successColor.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: AppConfig.successColor.withValues(alpha: 0.4)),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.phone_android, size: 12, color: AppConfig.successColor),
                            const SizedBox(width: 4),
                            Text(
                              lead.secondaryPhone!,
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: AppConfig.successColor,
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    TextButton.icon(
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        minimumSize: Size.zero,
                        tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      ),
                      onPressed: () => _showEditSecondaryDialog(context),
                      icon: const Icon(Icons.add, size: 14, color: AppConfig.primaryColor),
                      label: const Text(
                        '2nd Phone',
                        style: TextStyle(fontSize: 11, color: AppConfig.primaryColor, fontWeight: FontWeight.w600),
                      ),
                    ),
                ],
              ),

              // Notes Snippet (if available)
              if (lead.hasNotes) ...[
                const SizedBox(height: 8),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: AppConfig.surfaceElevated.withValues(alpha: 0.5),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.sticky_note_2_outlined, size: 12, color: Colors.amber),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          lead.notes!,
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppConfig.textSecondary,
                            fontStyle: FontStyle.italic,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  void _showEditSecondaryDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => SecondaryPhoneDialog(
        leadName: lead.name,
        initialPhone: lead.secondaryPhone,
        onSave: onSaveSecondaryPhone,
      ),
    );
  }
}
