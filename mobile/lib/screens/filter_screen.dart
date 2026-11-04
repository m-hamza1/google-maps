import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../state/leads_notifier.dart';

class FilterScreen extends StatefulWidget {
  final LeadsNotifier notifier;

  const FilterScreen({super.key, required this.notifier});

  @override
  State<FilterScreen> createState() => _FilterScreenState();
}

class _FilterScreenState extends State<FilterScreen> {
  late String _status;
  late String _category;
  late String _city;
  late double? _minRating;
  late bool _onlyWithPhone;

  @override
  void initState() {
    super.initState();
    _status = widget.notifier.selectedStatus;
    _category = widget.notifier.selectedCategory;
    _city = widget.notifier.selectedCity;
    _minRating = widget.notifier.minRating;
    _onlyWithPhone = widget.notifier.onlyWithPhone;
  }

  void _applyFilters() {
    widget.notifier.setStatus(_status);
    widget.notifier.setCategory(_category);
    widget.notifier.setCity(_city);
    widget.notifier.setMinRating(_minRating);
    widget.notifier.setOnlyWithPhone(_onlyWithPhone);
    Navigator.pop(context);
  }

  void _resetFilters() {
    widget.notifier.resetFilters();
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Filter Leads'),
        actions: [
          TextButton(
            onPressed: _resetFilters,
            child: const Text('Reset', style: TextStyle(color: AppConfig.dangerColor)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Status Section
            _buildSectionHeader('Pipeline Status'),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                _buildStatusChip('all', 'All Statuses'),
                _buildStatusChip('new', 'New'),
                _buildStatusChip('contacted', 'Contacted'),
                _buildStatusChip('interested', 'Interested'),
                _buildStatusChip('not_interested', 'Not Interested'),
                _buildStatusChip('closed', 'Closed'),
              ],
            ),
            const SizedBox(height: 24),

            // City / Location Section
            _buildSectionHeader('Target City / Location'),
            if (widget.notifier.cities.isEmpty)
              const Text('No city data available yet.', style: TextStyle(color: AppConfig.textSecondary))
            else
              DropdownButtonFormField<String>(
                initialValue: widget.notifier.cities.contains(_city) ? _city : 'all',
                dropdownColor: AppConfig.surfaceColor,
                decoration: const InputDecoration(
                  labelText: 'Select City',
                  prefixIcon: Icon(Icons.location_on_outlined, size: 20),
                ),
                items: [
                  const DropdownMenuItem(value: 'all', child: Text('All Cities')),
                  ...widget.notifier.cities.map(
                    (c) => DropdownMenuItem(value: c, child: Text(c)),
                  ),
                ],
                onChanged: (val) {
                  if (val != null) setState(() => _city = val);
                },
              ),
            const SizedBox(height: 24),

            // Minimum Rating Section
            _buildSectionHeader('Minimum Rating'),
            Wrap(
              spacing: 8,
              children: [
                _buildRatingChip(null, 'Any Rating'),
                _buildRatingChip(4.0, '⭐ 4.0+'),
                _buildRatingChip(4.5, '⭐ 4.5+'),
                _buildRatingChip(4.8, '⭐ 4.8+'),
              ],
            ),
            const SizedBox(height: 24),

            // Categories Section
            _buildSectionHeader('Business Category'),
            if (widget.notifier.categories.isEmpty)
              const Text('No categories available yet.', style: TextStyle(color: AppConfig.textSecondary))
            else
              DropdownButtonFormField<String>(
                initialValue: widget.notifier.categories.contains(_category) ? _category : 'all',
                dropdownColor: AppConfig.surfaceColor,
                decoration: const InputDecoration(
                  labelText: 'Select Category',
                  prefixIcon: Icon(Icons.category_outlined, size: 20),
                ),
                items: [
                  const DropdownMenuItem(value: 'all', child: Text('All Categories')),
                  ...widget.notifier.categories.map(
                    (cat) => DropdownMenuItem(value: cat, child: Text(cat)),
                  ),
                ],
                onChanged: (val) {
                  if (val != null) setState(() => _category = val);
                },
              ),
            const SizedBox(height: 24),

            // Phone Toggle
            Card(
              child: SwitchListTile(
                title: const Text('With Phone Number Only', style: TextStyle(fontWeight: FontWeight.w600)),
                subtitle: const Text(
                  'Only show listings that have contact phone numbers extracted',
                  style: TextStyle(fontSize: 12, color: AppConfig.textSecondary),
                ),
                value: _onlyWithPhone,
                activeTrackColor: AppConfig.primaryColor,
                onChanged: (val) => setState(() => _onlyWithPhone = val),
              ),
            ),
            const SizedBox(height: 32),

            // Apply Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConfig.primaryColor,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: _applyFilters,
                child: const Text('Apply Filters', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(
        title,
        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppConfig.textPrimary),
      ),
    );
  }

  Widget _buildStatusChip(String value, String label) {
    final isSelected = _status == value;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      selectedColor: AppConfig.primaryColor.withValues(alpha: 0.2),
      onSelected: (_) => setState(() => _status = value),
    );
  }

  Widget _buildRatingChip(double? value, String label) {
    final isSelected = _minRating == value;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      selectedColor: AppConfig.warningColor.withValues(alpha: 0.2),
      onSelected: (_) => setState(() => _minRating = value),
    );
  }
}
