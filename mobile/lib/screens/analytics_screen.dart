import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../state/leads_notifier.dart';

class AnalyticsScreen extends StatefulWidget {
  final LeadsNotifier notifier;

  const AnalyticsScreen({super.key, required this.notifier});

  @override
  State<AnalyticsScreen> createState() => _AnalyticsScreenState();
}

class _AnalyticsScreenState extends State<AnalyticsScreen> {
  late final TextEditingController _urlController;

  @override
  void initState() {
    super.initState();
    _urlController = TextEditingController(text: AppConfig.baseUrl);
  }

  @override
  void dispose() {
    _urlController.dispose();
    super.dispose();
  }

  void _saveServerUrl() {
    final newUrl = _urlController.text.trim();
    if (newUrl.isNotEmpty) {
      AppConfig.baseUrl = newUrl;
      widget.notifier.fetchLeads(resetPage: true);
      widget.notifier.refreshStats();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('✓ Server API URL updated!'),
          backgroundColor: AppConfig.successColor,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: widget.notifier,
      builder: (context, _) {
        final stats = widget.notifier.stats;

        return Scaffold(
          appBar: AppBar(
            title: const Text('Pipeline Overview'),
            actions: [
              IconButton(
                icon: const Icon(Icons.refresh),
                onPressed: () => widget.notifier.refreshStats(),
              ),
            ],
          ),
          body: SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Stat Cards
                Row(
                  children: [
                    _buildStatCard('Total Leads', stats.totalLeads.toString(), AppConfig.primaryColor, Icons.people),
                    const SizedBox(width: 12),
                    _buildStatCard('With Phone', stats.withPrimaryPhone.toString(), AppConfig.secondaryColor, Icons.phone),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    _buildStatCard('2nd Phones', stats.withSecondaryPhone.toString(), AppConfig.successColor, Icons.phone_android),
                    const SizedBox(width: 12),
                    _buildStatCard('With Notes', stats.withNotes.toString(), Colors.amber, Icons.note),
                  ],
                ),

                const SizedBox(height: 24),
                const Text('Pipeline Status Breakdown', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                const SizedBox(height: 12),

                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        _buildStatusRow('New Leads', stats.statuses['new'] ?? 0, const Color(0xFF818CF8)),
                        const Divider(color: Color(0xFF334155), height: 16),
                        _buildStatusRow('Contacted', stats.statuses['contacted'] ?? 0, const Color(0xFF22D3EE)),
                        const Divider(color: Color(0xFF334155), height: 16),
                        _buildStatusRow('Interested', stats.statuses['interested'] ?? 0, const Color(0xFF34D399)),
                        const Divider(color: Color(0xFF334155), height: 16),
                        _buildStatusRow('Not Interested', stats.statuses['not_interested'] ?? 0, const Color(0xFFF87171)),
                        const Divider(color: Color(0xFF334155), height: 16),
                        _buildStatusRow('Closed / Converted', stats.statuses['closed'] ?? 0, const Color(0xFF94A3B8)),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 24),
                const Text('Top Business Categories', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                const SizedBox(height: 12),

                if (stats.topCategories.isEmpty)
                  const Card(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: Text('No categories recorded yet.', style: TextStyle(color: AppConfig.textSecondary)),
                    ),
                  )
                else
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: stats.topCategories.map((c) {
                          return Padding(
                            padding: const EdgeInsets.symmetric(vertical: 6),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(c.category, style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.white)),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: AppConfig.surfaceElevated,
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text('${c.count} leads', style: const TextStyle(fontSize: 12, color: AppConfig.textSecondary)),
                                ),
                              ],
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                  ),

                const SizedBox(height: 24),
                const Text('Backend Server Connection', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                const SizedBox(height: 6),
                const Text(
                  'Change this IP if testing on a real physical Android phone connected over Wi-Fi.',
                  style: TextStyle(fontSize: 12, color: AppConfig.textSecondary),
                ),
                const SizedBox(height: 12),

                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        TextField(
                          controller: _urlController,
                          decoration: const InputDecoration(
                            labelText: 'Laravel API Base URL',
                            prefixIcon: Icon(Icons.link, color: AppConfig.primaryColor),
                          ),
                        ),
                        const SizedBox(height: 12),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppConfig.surfaceElevated,
                              foregroundColor: Colors.white,
                            ),
                            onPressed: _saveServerUrl,
                            child: const Text('Update Server URL'),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildStatCard(String label, String value, Color accent, IconData icon) {
    return Expanded(
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(label, style: const TextStyle(fontSize: 12, color: AppConfig.textSecondary, fontWeight: FontWeight.w600)),
                  Icon(icon, size: 16, color: accent),
                ],
              ),
              const SizedBox(height: 10),
              Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: accent)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusRow(String label, int count, Color dotColor) {
    return Row(
      children: [
        Container(width: 8, height: 8, decoration: BoxDecoration(color: dotColor, shape: BoxShape.circle)),
        const SizedBox(width: 10),
        Expanded(child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: Colors.white))),
        Text(count.toString(), style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: dotColor)),
      ],
    );
  }
}
