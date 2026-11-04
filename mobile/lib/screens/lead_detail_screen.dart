import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../config/app_config.dart';
import '../models/lead.dart';
import '../state/leads_notifier.dart';
import '../widgets/status_badge.dart';

class LeadDetailScreen extends StatefulWidget {
  final Lead lead;
  final LeadsNotifier notifier;

  const LeadDetailScreen({
    super.key,
    required this.lead,
    required this.notifier,
  });

  @override
  State<LeadDetailScreen> createState() => _LeadDetailScreenState();
}

class _LeadDetailScreenState extends State<LeadDetailScreen> {
  late final TextEditingController _secondaryPhoneController;
  late final TextEditingController _notesController;
  late String _currentStatus;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _secondaryPhoneController = TextEditingController(text: widget.lead.secondaryPhone ?? '');
    _notesController = TextEditingController(text: widget.lead.notes ?? '');
    _currentStatus = widget.lead.status;
  }

  @override
  void dispose() {
    _secondaryPhoneController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _makeCall(String phone) async {
    final cleanDigits = phone.replaceAll(RegExp(r'[^\d+]'), '');
    final uri = Uri.parse('tel:$cleanDigits');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _openUrl(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  Future<void> _saveChanges() async {
    setState(() => _isSaving = true);

    final success = await widget.notifier.updateLead(
      widget.lead.id,
      secondaryPhone: _secondaryPhoneController.text.trim(),
      notes: _notesController.text.trim(),
      status: _currentStatus,
    );

    setState(() => _isSaving = false);

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            success ? '✓ Lead updated successfully!' : '⚠️ Failed to update lead. Please try again.',
          ),
          backgroundColor: success ? AppConfig.successColor : AppConfig.dangerColor,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Lead Profile'),
        actions: [
          IconButton(
            icon: _isSaving
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                  )
                : const Icon(Icons.check, color: AppConfig.successColor),
            onPressed: _isSaving ? null : _saveChanges,
            tooltip: 'Save Changes',
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Business Header Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            widget.lead.name,
                            style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        StatusBadge(status: _currentStatus),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Rating & Category
                    Row(
                      children: [
                        if (widget.lead.category != null) ...[
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: AppConfig.surfaceElevated,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              widget.lead.category!,
                              style: const TextStyle(
                                fontSize: 12,
                                color: AppConfig.textSecondary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                        ],
                        if (widget.lead.rating != null) ...[
                          Row(
                            children: [
                              const Icon(Icons.star, color: Colors.amber, size: 16),
                              const SizedBox(width: 4),
                              Text(
                                widget.lead.rating!.toStringAsFixed(1),
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.amber,
                                ),
                              ),
                              if (widget.lead.reviewsCount > 0)
                                Text(
                                  ' (${widget.lead.reviewsCount} reviews)',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: AppConfig.textSecondary,
                                  ),
                                ),
                            ],
                          ),
                        ],
                      ],
                    ),

                    if (widget.lead.address != null) ...[
                      const SizedBox(height: 12),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Icon(Icons.location_on, size: 16, color: AppConfig.textSecondary),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              widget.lead.address!,
                              style: const TextStyle(fontSize: 13, color: AppConfig.textSecondary),
                            ),
                          ),
                        ],
                      ),
                    ],

                    if (widget.lead.website != null) ...[
                      const SizedBox(height: 12),
                      InkWell(
                        onTap: () => _openUrl(widget.lead.website!),
                        child: Row(
                          children: [
                            const Icon(Icons.language, size: 16, color: AppConfig.primaryColor),
                            const SizedBox(width: 6),
                            Expanded(
                              child: Text(
                                widget.lead.website!,
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: AppConfig.primaryColor,
                                  decoration: TextDecoration.underline,
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

            const SizedBox(height: 16),

            // Quick Call Actions Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Direct Calling Actions',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppConfig.textSecondary),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        // Call Primary Phone
                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppConfig.primaryColor,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            ),
                            onPressed: widget.lead.hasPhone
                                ? () => _makeCall(widget.lead.primaryPhone!)
                                : null,
                            icon: const Icon(Icons.call, size: 18),
                            label: Text(
                              widget.lead.hasPhone ? 'Call Primary' : 'No Primary',
                              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        // Call Secondary Phone
                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppConfig.successColor,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            ),
                            onPressed: _secondaryPhoneController.text.isNotEmpty
                                ? () => _makeCall(_secondaryPhoneController.text)
                                : null,
                            icon: const Icon(Icons.phone_android, size: 18),
                            label: const Text(
                              'Call 2nd Phone',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Secondary Phone Input Card (Specifically requested by user!)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: const [
                        Icon(Icons.contact_phone_outlined, size: 18, color: AppConfig.secondaryColor),
                        SizedBox(width: 8),
                        Text(
                          'Secondary Phone Number',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: Colors.white),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'Save an alternate contact number against this specific business lead.',
                      style: TextStyle(fontSize: 12, color: AppConfig.textSecondary),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _secondaryPhoneController,
                      keyboardType: TextInputType.phone,
                      decoration: const InputDecoration(
                        hintText: 'e.g. +1 555-0199 or 0300-1234567',
                        prefixIcon: Icon(Icons.phone_iphone, color: AppConfig.secondaryColor, size: 20),
                      ),
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Workflow Status Selector
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Lead Pipeline Status',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppConfig.textSecondary),
                    ),
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        _buildStatusOption('new', 'New'),
                        _buildStatusOption('contacted', 'Contacted'),
                        _buildStatusOption('interested', 'Interested'),
                        _buildStatusOption('not_interested', 'Not Interested'),
                        _buildStatusOption('closed', 'Closed'),
                      ],
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Lead Notes Card (Specifically requested by user!)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: const [
                        Icon(Icons.notes_outlined, size: 18, color: Colors.amber),
                        SizedBox(width: 8),
                        Text(
                          'Lead Notes & Comments',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: Colors.white),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'Attach personal notes, conversation logs, or custom attributes to this lead.',
                      style: TextStyle(fontSize: 12, color: AppConfig.textSecondary),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _notesController,
                      maxLines: 5,
                      decoration: const InputDecoration(
                        hintText: 'Type your notes here... (e.g. Spoke with manager, callback on Monday)',
                      ),
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 24),

            // Full-Width Save Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConfig.primaryColor,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: _isSaving ? null : _saveChanges,
                icon: const Icon(Icons.save),
                label: Text(
                  _isSaving ? 'Saving Changes...' : 'Save Lead Updates',
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                ),
              ),
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusOption(String key, String label) {
    final isSelected = _currentStatus == key;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => setState(() => _currentStatus = key),
      selectedColor: AppConfig.primaryColor,
      backgroundColor: AppConfig.surfaceElevated,
      labelStyle: TextStyle(
        fontSize: 12,
        fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
        color: isSelected ? Colors.white : AppConfig.textSecondary,
      ),
    );
  }
}
