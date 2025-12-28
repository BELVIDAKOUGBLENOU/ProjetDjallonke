import 'package:isar/isar.dart';

part 'community.g.dart';

@Collection()
class Community {
  Id id = Isar.autoIncrement;

  String? name;
  DateTime? creationDate;
  int? createdBy;
  int? countryId;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final memberships = IsarLinks<dynamic>();
  final premises = IsarLinks<Premise>();
  final country = IsarLink<dynamic>();

  Community({
    this.name,
    this.creationDate,
    this.createdBy,
    this.countryId,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory Community.fromJson(Map<String, dynamic> json) {
    return Community(
      name: json['name'] as String?,
      creationDate: json['creation_date'] == null
          ? null
          : DateTime.parse(json['creation_date'] as String),
      createdBy: json['created_by'] as int?,
      countryId: json['country_id'] as int?,
      createdAt: json['created_at'] == null
          ? null
          : DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] == null
          ? null
          : DateTime.parse(json['updated_at'] as String),
      deletedAt: json['deleted_at'] == null
          ? null
          : DateTime.parse(json['deleted_at'] as String),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'creation_date': creationDate?.toIso8601String(),
      'created_by': createdBy,
      'country_id': countryId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'deleted_at': deletedAt?.toIso8601String(),
    };
  }
}

// Simple forward reference to Premise to avoid circular import; keep real model in premises.dart
class Premise {}
