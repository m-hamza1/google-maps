class Lead {
  final int id;
  final String placeId;
  final String name;
  final String? primaryPhone;
  final String? secondaryPhone;
  final double? rating;
  final int reviewsCount;
  final String? address;
  final String? city;
  final String? category;
  final String? website;
  final double? latitude;
  final double? longitude;
  final String? notes;
  final String status;
  final String? searchQuery;
  final String source;
  final DateTime? createdAt;

  Lead({
    required this.id,
    required this.placeId,
    required this.name,
    this.primaryPhone,
    this.secondaryPhone,
    this.rating,
    this.reviewsCount = 0,
    this.address,
    this.city,
    this.category,
    this.website,
    this.latitude,
    this.longitude,
    this.notes,
    this.status = 'new',
    this.searchQuery,
    this.source = 'google_maps',
    this.createdAt,
  });

  factory Lead.fromJson(Map<String, dynamic> json) {
    return Lead(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      placeId: json['place_id'] ?? '',
      name: json['name'] ?? '',
      primaryPhone: json['primary_phone'],
      secondaryPhone: json['secondary_phone'],
      rating: json['rating'] != null ? (json['rating'] as num).toDouble() : null,
      reviewsCount: json['reviews_count'] != null ? (json['reviews_count'] as num).toInt() : 0,
      address: json['address'],
      city: json['city'],
      category: json['category'],
      website: json['website'],
      latitude: json['latitude'] != null ? (json['latitude'] as num).toDouble() : null,
      longitude: json['longitude'] != null ? (json['longitude'] as num).toDouble() : null,
      notes: json['notes'],
      status: json['status'] ?? 'new',
      searchQuery: json['search_query'],
      source: json['source'] ?? 'google_maps',
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'place_id': placeId,
      'name': name,
      'primary_phone': primaryPhone,
      'secondary_phone': secondaryPhone,
      'rating': rating,
      'reviews_count': reviewsCount,
      'address': address,
      'city': city,
      'category': category,
      'website': website,
      'latitude': latitude,
      'longitude': longitude,
      'notes': notes,
      'status': status,
      'search_query': searchQuery,
      'source': source,
    };
  }

  Lead copyWith({
    String? secondaryPhone,
    String? notes,
    String? status,
  }) {
    return Lead(
      id: id,
      placeId: placeId,
      name: name,
      primaryPhone: primaryPhone,
      secondaryPhone: secondaryPhone ?? this.secondaryPhone,
      rating: rating,
      reviewsCount: reviewsCount,
      address: address,
      city: city,
      category: category,
      website: website,
      latitude: latitude,
      longitude: longitude,
      notes: notes ?? this.notes,
      status: status ?? this.status,
      searchQuery: searchQuery,
      source: source,
      createdAt: createdAt,
    );
  }

  bool get hasPhone => primaryPhone != null && primaryPhone!.isNotEmpty;
  bool get hasSecondaryPhone => secondaryPhone != null && secondaryPhone!.isNotEmpty;
  bool get hasNotes => notes != null && notes!.trim().isNotEmpty;
}
