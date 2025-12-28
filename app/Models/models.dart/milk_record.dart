import 'package:isar/isar.dart';

part 'milk_record.g.dart';

@Collection()
class MilkRecord {
  Id id = Isar.autoIncrement;

  int? eventId;
  double? volumeLiters;
  String? period;

  final event = IsarLink<dynamic>();

  MilkRecord({this.eventId, this.volumeLiters, this.period});

  factory MilkRecord.fromJson(Map<String, dynamic> json) {
    return MilkRecord(
      eventId: json['event_id'] as int?,
      volumeLiters: json['volume_liters'] == null
          ? null
          : (json['volume_liters'] as num).toDouble(),
      period: json['period'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'event_id': eventId,
        'volume_liters': volumeLiters,
        'period': period,
      };
  // Event attributes
  int? createdBy;
  int? confirmedBy;
  int? animalId;
  String? source;
  DateTime? eventDate;
  String? comment;
  bool? isConfirmed;
  String? uid;
  int? version;

  // Milk specific
  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();

  MilkRecord({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
    this.volumeLiters,
    this.period,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory MilkRecord.fromJson(Map<String, dynamic> json) {
    return MilkRecord(
      createdBy: json['created_by'] as int?,
      confirmedBy: json['confirmed_by'] as int?,
      animalId: json['animal_id'] as int?,
      source: json['source'] as String?,
      eventDate: json['event_date'] == null ? null : DateTime.parse(json['event_date'] as String),
      comment: json['comment'] as String?,
      isConfirmed: json['is_confirmed'] == null ? null : (json['is_confirmed'] as bool),
      uid: json['uid'] as String?,
      version: json['version'] is int ? json['version'] as int : (json['version'] != null ? int.tryParse(json['version'].toString()) : null),
      volumeLiters: json['volume_liters'] == null ? null : (json['volume_liters'] as num).toDouble(),
      period: json['period'] as String?,
      createdAt: json['created_at'] == null ? null : DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] == null ? null : DateTime.parse(json['updated_at'] as String),
      deletedAt: json['deleted_at'] == null ? null : DateTime.parse(json['deleted_at'] as String),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'created_by': createdBy,
        'confirmed_by': confirmedBy,
        'animal_id': animalId,
        'source': source,
        'event_date': eventDate?.toIso8601String(),
        'comment': comment,
        'is_confirmed': isConfirmed,
        'uid': uid,
        'version': version,
        'volume_liters': volumeLiters,
        'period': period,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'deleted_at': deletedAt?.toIso8601String(),
      };
}
