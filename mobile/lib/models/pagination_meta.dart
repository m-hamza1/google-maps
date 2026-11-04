class PaginationMeta {
  final int currentPage;
  final int lastPage;
  final int perPage;
  final int total;
  final bool hasMore;

  PaginationMeta({
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
    required this.hasMore,
  });

  factory PaginationMeta.fromJson(Map<String, dynamic> json) {
    return PaginationMeta(
      currentPage: json['current_page'] ?? 1,
      lastPage: json['last_page'] ?? 1,
      perPage: json['per_page'] ?? 15,
      total: json['total'] ?? 0,
      hasMore: json['has_more'] ?? false,
    );
  }

  factory PaginationMeta.empty() {
    return PaginationMeta(
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0,
      hasMore: false,
    );
  }
}
