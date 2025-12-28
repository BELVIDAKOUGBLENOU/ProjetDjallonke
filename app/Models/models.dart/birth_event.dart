import 'package:isar/isar.dart';

part 'birth_event.g.dart';

@Collection()
class BirthEvent {
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

  // Birth specific
  int? motherId;
  int? fatherId;
  int? nbAlive;
  int? nbDead;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();
  final mother = IsarLink<dynamic>();
  final father = IsarLink<dynamic>();

  BirthEvent({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
    this.motherId,
    this.fatherId,
    this.nbAlive,
    this.nbDead,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory BirthEvent.fromJson(Map<String, dynamic> json) {
    return BirthEvent(
      createdBy: json['created_by'] as int?,
      confirmedBy: json['confirmed_by'] as int?,
      animalId: json['animal_id'] as int?,
      source: json['source'] as String?,
      eventDate: json['event_date'] == null ? null : DateTime.parse(json['event_date'] as String),
      comment: json['comment'] as String?,
      isConfirmed: json['is_confirmed'] == null ? null : (json['is_confirmed'] as bool),
      uid: json['uid'] as String?,
      version: json['version'] is int ? json['version'] as int : (json['version'] != null ? int.tryParse(json['version'].toString()) : null),
      motherId: json['mother_id'] as int?,
      fatherId: json['father_id'] as int?,
      nbAlive: json['nb_alive'] as int?,
      nbDead: json['nb_dead'] as int?,
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
        'mother_id': motherId,
        'father_id': fatherId,
        'nb_alive': nbAlive,
        'nb_dead': nbDead,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'deleted_at': deletedAt?.toIso8601String(),
      };
}
