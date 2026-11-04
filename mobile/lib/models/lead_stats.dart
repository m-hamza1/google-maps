class LeadStats {
  final int totalLeads;
  final int withPrimaryPhone;
  final int withSecondaryPhone;
  final int withNotes;
  final Map<String, int> statuses;
  final List<CategoryCount> topCategories;

  LeadStats({
    required this.totalLeads,
    required this.withPrimaryPhone,
    required this.withSecondaryPhone,
    required this.withNotes,
    required this.statuses,
    required this.topCategories,
  });

  factory LeadStats.fromJson(Map<String, dynamic> json) {
    final statusMap = <String, int>{};
    if (json['statuses'] is Map) {
      (json['statuses'] as Map).forEach((k, v) {
        statusMap[k.toString()] = (v as num).toInt();
      });
    }

    final catList = <CategoryCount>[];
    if (json['top_categories'] is List) {
      for (final item in json['top_categories']) {
        if (item is Map) {
          catList.add(CategoryCount(
            category: item['category'] ?? 'Other',
            count: (item['count'] as num).toInt(),
          ));
        }
      }
    }

    return LeadStats(
      totalLeads: json['total_leads'] ?? 0,
      withPrimaryPhone: json['with_primary_phone'] ?? 0,
      withSecondaryPhone: json['with_secondary_phone'] ?? 0,
      withNotes: json['with_notes'] ?? 0,
      statuses: statusMap,
      topCategories: catList,
    );
  }

  factory LeadStats.empty() {
    return LeadStats(
      totalLeads: 0,
      withPrimaryPhone: 0,
      withSecondaryPhone: 0,
      withNotes: 0,
      statuses: {'new': 0, 'contacted': 0, 'interested': 0, 'not_interested': 0, 'closed': 0},
      topCategories: [],
    );
  }
}

class CategoryCount {
  final String category;
  final int count;

  CategoryCount({required this.category, required this.count});
}
