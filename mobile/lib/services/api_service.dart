import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/lead.dart';
import '../models/lead_stats.dart';
import '../models/pagination_meta.dart';

class ApiService {
  final http.Client _client;

  ApiService({http.Client? client}) : _client = client ?? http.Client();

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      };

  /// Fetch leads with search, filters, and pagination
  Future<({List<Lead> leads, PaginationMeta pagination})> fetchLeads({
    String? search,
    String? status,
    String? category,
    String? city,
    double? minRating,
    bool? hasPhone,
    int page = 1,
    int perPage = 15,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
    };

    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }
    if (status != null && status != 'all' && status.isNotEmpty) {
      queryParams['status'] = status;
    }
    if (category != null && category != 'all' && category.isNotEmpty) {
      queryParams['category'] = category;
    }
    if (city != null && city != 'all' && city.isNotEmpty) {
      queryParams['city'] = city;
    }
    if (minRating != null && minRating > 0) {
      queryParams['min_rating'] = minRating.toString();
    }
    if (hasPhone == true) {
      queryParams['has_phone'] = '1';
    }

    final uri = Uri.parse('${AppConfig.baseUrl}/leads').replace(queryParameters: queryParams);

    try {
      final response = await _client.get(uri, headers: _headers).timeout(const Duration(seconds: 15));

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final body = json.decode(response.body);
        final rawList = body['data'] as List? ?? [];
        final leads = rawList.map((item) => Lead.fromJson(item as Map<String, dynamic>)).toList();
        final pagination = body['pagination'] != null
            ? PaginationMeta.fromJson(body['pagination'] as Map<String, dynamic>)
            : PaginationMeta.empty();

        return (leads: leads, pagination: pagination);
      } else {
        throw HttpException('Server returned error ${response.statusCode}: ${response.body}');
      }
    } on SocketException {
      throw const SocketException('Cannot connect to backend server. Make sure Laravel is running.');
    } catch (e) {
      rethrow;
    }
  }

  /// Update secondary phone, notes, or status
  Future<Lead> updateLead(
    int leadId, {
    String? secondaryPhone,
    String? notes,
    String? status,
  }) async {
    final uri = Uri.parse('${AppConfig.baseUrl}/leads/$leadId');
    final payload = <String, dynamic>{};

    if (secondaryPhone != null) payload['secondary_phone'] = secondaryPhone;
    if (notes != null) payload['notes'] = notes;
    if (status != null) payload['status'] = status;

    try {
      final response = await _client.patch(
        uri,
        headers: _headers,
        body: json.encode(payload),
      ).timeout(const Duration(seconds: 15));

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final body = json.decode(response.body);
        return Lead.fromJson(body['data'] as Map<String, dynamic>);
      } else {
        throw HttpException('Failed to update lead (${response.statusCode}): ${response.body}');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Fetch dashboard and pipeline metrics
  Future<LeadStats> fetchStats() async {
    final uri = Uri.parse('${AppConfig.baseUrl}/leads/stats');

    try {
      final response = await _client.get(uri, headers: _headers).timeout(const Duration(seconds: 15));

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final body = json.decode(response.body);
        return LeadStats.fromJson(body['data'] as Map<String, dynamic>);
      } else {
        return LeadStats.empty();
      }
    } catch (_) {
      return LeadStats.empty();
    }
  }

  /// Fetch categories list for filter dropdown
  Future<List<String>> fetchCategories() async {
    final uri = Uri.parse('${AppConfig.baseUrl}/leads/categories');

    try {
      final response = await _client.get(uri, headers: _headers).timeout(const Duration(seconds: 10));

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final body = json.decode(response.body);
        final list = body['data'] as List? ?? [];
        return list.map((e) => e.toString()).toList();
      }
      return [];
    } catch (_) {
      return [];
    }
  }

  /// Fetch cities list for filter dropdown
  Future<List<String>> fetchCities() async {
    final uri = Uri.parse('${AppConfig.baseUrl}/leads/cities');

    try {
      final response = await _client.get(uri, headers: _headers).timeout(const Duration(seconds: 10));

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final body = json.decode(response.body);
        final list = body['data'] as List? ?? [];
        return list.map((e) => e.toString()).toList();
      }
      return [];
    } catch (_) {
      return [];
    }
  }
}
