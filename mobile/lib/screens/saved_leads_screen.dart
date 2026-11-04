import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../state/leads_notifier.dart';
import '../widgets/lead_card.dart';
import 'lead_detail_screen.dart';

class SavedLeadsScreen extends StatelessWidget {
  final LeadsNotifier notifier;

  const SavedLeadsScreen({super.key, required this.notifier});

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: notifier,
      builder: (context, _) {
        // Filter leads that have secondary phone or notes or status != 'new'
        final actionedLeads = notifier.leads.where((l) {
          final hasSec = l.hasSecondaryPhone;
          final hasNotes = l.hasNotes;
          final isActioned = l.status != 'new';
          return hasSec || hasNotes || isActioned;
        }).toList();

        return Scaffold(
          appBar: AppBar(
            title: const Text('Actioned Leads'),
            actions: [
              IconButton(
                icon: const Icon(Icons.refresh),
                onPressed: () => notifier.fetchLeads(resetPage: true),
              ),
            ],
          ),
          body: actionedLeads.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.bookmark_border, size: 64, color: AppConfig.textSecondary.withValues(alpha: 0.5)),
                        const SizedBox(height: 16),
                        const Text(
                          'No Actioned Leads Yet',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: Colors.white),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'When you add a secondary phone number, write notes, or update a lead status, they will appear here.',
                          textAlign: TextAlign.center,
                          style: TextStyle(fontSize: 13, color: AppConfig.textSecondary),
                        ),
                      ],
                    ),
                  ),
                )
              : ListView.builder(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  itemCount: actionedLeads.length,
                  itemBuilder: (context, index) {
                    final lead = actionedLeads[index];
                    return LeadCard(
                      lead: lead,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (ctx) => LeadDetailScreen(
                              lead: lead,
                              notifier: notifier,
                            ),
                          ),
                        );
                      },
                      onSaveSecondaryPhone: (phone) {
                        notifier.updateLead(lead.id, secondaryPhone: phone);
                      },
                    );
                  },
                ),
        );
      },
    );
  }
}
