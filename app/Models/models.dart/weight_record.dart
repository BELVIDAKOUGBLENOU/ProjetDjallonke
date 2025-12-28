import 'package:isar/isar.dart';

part 'weight_record.g.dart';

@Collection()
class WeightRecord {
  Id id = Isar.autoIncrement;

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

  // Weight specific
  double? weight;
  int? ageDays;
  String? measureMethod;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();

  WeightRecord({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
    this.weight,
    this.ageDays,
    this.measureMethod,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory WeightRecord.fromJson(Map<String, dynamic> json) {
    return WeightRecord(
      createdBy: json['created_by'] as int?,
      confirmedBy: json['confirmed_by'] as int?,
      animalId: json['animal_id'] as int?,
      source: json['source'] as String?,
      eventDate: json['event_date'] == null ? null : DateTime.parse(json['event_date'] as String),
      comment: json['comment'] as String?,
      isConfirmed: json['is_confirmed'] == null ? null : (json['is_confirmed'] as bool),
      uid: json['uid'] as String?,
      version: json['version'] is int ? json['version'] as int : (json['version'] != null ? int.tryParse(json['version'].toString()) : null),
      weight: json['weight'] == null ? null : (json['weight'] as num).toDouble(),
      ageDays: json['age_days'] as int?,
      measureMethod: json['measure_method'] as String?,
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
        'weight': weight,
        'age_days': ageDays,
        'measure_method': measureMethod,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'deleted_at': deletedAt?.toIso8601String(),
      };
}
