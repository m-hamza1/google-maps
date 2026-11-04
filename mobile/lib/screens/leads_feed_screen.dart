import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../state/leads_notifier.dart';
import '../widgets/lead_card.dart';
import 'filter_screen.dart';
import 'lead_detail_screen.dart';

class LeadsFeedScreen extends StatefulWidget {
  final LeadsNotifier notifier;

  const LeadsFeedScreen({super.key, required this.notifier});

  @override
  State<LeadsFeedScreen> createState() => _LeadsFeedScreenState();
}

class _LeadsFeedScreenState extends State<LeadsFeedScreen> {
  final ScrollController _scrollController = ScrollController();
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      widget.notifier.fetchLeads(resetPage: false);
    }
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: widget.notifier,
      builder: (context, _) {
        final notifier = widget.notifier;

        return Scaffold(
          appBar: AppBar(
            title: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: AppConfig.primaryColor,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(Icons.radar, color: Colors.white, size: 20),
                ),
                const SizedBox(width: 10),
                const Text('LeadRadar'),
              ],
            ),
            actions: [
              IconButton(
                icon: Badge(
                  isLabelVisible: notifier.hasActiveFilters,
                  child: const Icon(Icons.tune),
                ),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (ctx) => FilterScreen(notifier: notifier),
                    ),
                  );
                },
              ),
              IconButton(
                icon: const Icon(Icons.refresh),
                onPressed: () => notifier.fetchLeads(resetPage: true),
              ),
            ],
          ),
          body: Column(
            children: [
              // Search Input
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                child: TextField(
                  controller: _searchController,
                  onChanged: (val) => notifier.setSearch(val),
                  decoration: InputDecoration(
                    hintText: 'Search name, phone, address, notes...',
                    prefixIcon: const Icon(Icons.search, color: AppConfig.textSecondary),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear, size: 18),
                            onPressed: () {
                              _searchController.clear();
                              notifier.setSearch('');
                            },
                          )
                        : null,
                    contentPadding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                ),
              ),

              // Status Filter Chips
              SizedBox(
                height: 42,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  children: [
                    _buildChip('All', 'all', notifier),
                    _buildChip('New', 'new', notifier),
                    _buildChip('Contacted', 'contacted', notifier),
                    _buildChip('Interested', 'interested', notifier),
                    _buildChip('Closed', 'closed', notifier),
                  ],
                ),
              ),

              const SizedBox(height: 6),

              // Error State Banner
              if (notifier.errorMessage != null)
                Container(
                  margin: const EdgeInsets.all(16),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppConfig.dangerColor.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppConfig.dangerColor.withValues(alpha: 0.3)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.error_outline, color: AppConfig.dangerColor, size: 20),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          notifier.errorMessage!,
                          style: const TextStyle(color: Color(0xFFFCA5A5), fontSize: 12),
                        ),
                      ),
                      TextButton(
                        onPressed: () => notifier.fetchLeads(resetPage: true),
                        child: const Text('Retry', style: TextStyle(color: Colors.white, fontSize: 12)),
                      ),
                    ],
                  ),
                ),

              // Leads Feed List
              Expanded(
                child: RefreshIndicator(
                  color: AppConfig.primaryColor,
                  onRefresh: () => notifier.fetchLeads(resetPage: true),
                  child: notifier.isLoading
                      ? const Center(child: CircularProgressIndicator())
                      : notifier.leads.isEmpty
                          ? _buildEmptyState(notifier)
                          : ListView.builder(
                              controller: _scrollController,
                              physics: const AlwaysScrollableScrollPhysics(),
                              itemCount: notifier.leads.length + (notifier.isLoadingMore ? 1 : 0),
                              itemBuilder: (context, index) {
                                if (index == notifier.leads.length) {
                                  return const Padding(
                                    padding: EdgeInsets.symmetric(vertical: 24),
                                    child: Center(child: CircularProgressIndicator()),
                                  );
                                }

                                final lead = notifier.leads[index];
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
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildChip(String label, String value, LeadsNotifier notifier) {
    final isSelected = notifier.selectedStatus == value;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (_) => notifier.setStatus(value),
        selectedColor: AppConfig.primaryColor.withValues(alpha: 0.25),
        checkmarkColor: AppConfig.primaryColor,
        labelStyle: TextStyle(
          fontSize: 12,
          fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
          color: isSelected ? Colors.white : AppConfig.textSecondary,
        ),
        backgroundColor: AppConfig.surfaceColor,
        side: BorderSide(
          color: isSelected ? AppConfig.primaryColor : const Color(0xFF334155),
        ),
      ),
    );
  }

  Widget _buildEmptyState(LeadsNotifier notifier) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.person_search_outlined, size: 64, color: AppConfig.textSecondary.withValues(alpha: 0.5)),
            const SizedBox(height: 16),
            const Text(
              'No Leads Found',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: Colors.white),
            ),
            const SizedBox(height: 8),
            Text(
              notifier.hasActiveFilters
                  ? 'No results match the current filters. Try resetting your search filters.'
                  : 'Start scraping from the Laravel admin panel to see your Google Maps leads here.',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 13, color: AppConfig.textSecondary),
            ),
            if (notifier.hasActiveFilters) ...[
              const SizedBox(height: 16),
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConfig.surfaceElevated,
                  foregroundColor: Colors.white,
                ),
                onPressed: () {
                  _searchController.clear();
                  notifier.resetFilters();
                },
                icon: const Icon(Icons.clear, size: 16),
                label: const Text('Reset Filters'),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
